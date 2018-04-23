<?php

namespace Mss\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Mss\DataTables\ArticleDataTable;
use Mss\DataTables\AssignOrderDataTable;
use Mss\DataTables\OrderDataTable;
use Mss\Http\Requests\OrderRequest;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Delivery;
use Mss\Models\Order;
use Mss\Models\OrderItem;
use Mss\Models\OrderMessage;
use Mss\Models\Supplier;
use Mss\Models\User;
use Mss\Notifications\NewDeliverySaved;
use Mss\Services\PrintLabelService;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(OrderDataTable $orderDataTable) {
        $unassignedMessages = OrderMessage::unassigned()->count();

        return $orderDataTable->render('order.list', compact('unassignedMessages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $articles = $this->getArticleList();

        $order = new Order();
        $order->internal_order_number = $order->getNextInternalOrderNumber();
        $order->order_date = Carbon::now();
        $order->save();

        if ($request->has('article')) {
            $preSetArticles = Article::withCurrentSupplierArticle()->find($request->get('article'));
            $preSetOrderItems = $preSetArticles->map(function ($article) {
                return [
                    'article_id' => $article->id,
                    'quantity' => $article->currentSupplierArticle->order_quantity ?? '',
                    'price' => $article->currentSupplierArticle->price ? $article->currentSupplierArticle->price / 100 : ''
                ];
            });

            $order->supplier_id = $preSetArticles->first()->currentSupplierArticle->supplier_id;
            $order->items = $preSetOrderItems;
        }

        return view('order.create', compact('order', 'articles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request) {
        /* @var $order Order */
        $order = Order::findOrFail($request->get('order_id'));

        $order->status = $request->get('status');
        $order->supplier_id = $request->get('supplier');
        $order->external_order_number = $request->get('external_order_number');
        $order->total_cost = parsePrice($request->get('total_cost'));
        $order->shipping_cost = parsePrice($request->get('shipping_cost')) ?? 0;
        $order->order_date = !empty($request->get('order_date')) ? Carbon::parse($request->get('order_date')) : null;
        $order->expected_delivery = !empty($request->get('expected_delivery')) ? Carbon::parse($request->get('expected_delivery')) : null;
        $order->notes = $request->get('notes');
        $order->confirmation_received = $request->get('confirmation_received') ?? false;
        $order->invoice_received = $request->get('invoice_received') ?? false;

        if ($order->status === Order::STATUS_NEW) {
            $order->status = Order::STATUS_ORDERED;
        }

        $order->save();

        $order->items()->delete();
        collect($request->get('article'))->each(function ($article, $key) use ($order, $request) {
            $quantity = intval($request->get('quantity')[$key] ?: 0);
            $price = $request->get('price')[$key] ?: null;

            if (empty($article) || empty($quantity)) {
                return true;
            }

            $order->items()->create([
                'article_id' => $article,
                'price' => parsePrice($price),
                'quantity' => $quantity
            ]);
        });

        flash('Bestellung gespeichert', 'success');
        return redirect()->route('order.show', $order);
    }

    public function confirmationReceived(Order $order) {
        $order->confirmation_received = true;
        $order->save();

        return redirect()->route('order.show', $order);
    }

    public function invoiceReceived(Order $order) {
        $order->invoice_received = true;
        $order->save();

        return redirect()->route('order.show', $order);
    }

    public function cancel(Order $order) {
        $order->delete();

        return redirect()->route('order.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, AssignOrderDataTable $assignOrderDataTable) {
        $order = Order::with('items.order.items')->findOrFail($id);
        $audits = $order->getAudits();
        $messages = $order->messages()->latest('received')->get();

        return $assignOrderDataTable->render('order.show', compact('order', 'audits', 'messages'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $articles = $this->getArticleList();
        $order = Order::findOrFail($id);

        return view('order.edit', compact('order', 'articles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        Order::findOrFail($id)->delete();

        flash('Bestellung gelöscht', 'success');
        return redirect()->route('order.index');
    }

    public function articleList(Supplier $supplier) {
        return response()->json($supplier->articles->pluck(['name', 'id']));
    }

    public function createDelivery(Order $order) {
        return view('order.delivery_form', compact('order'));
    }

    public function storeDelivery(Order $order, Request $request) {
        /* @var Delivery $delivery */
        $delivery = $order->deliveries()->create([
            'delivery_date' => Carbon::parse($request->get('delivery_date')),
            'delivery_note_number' => $request->get('delivery_note_number'),
            'notes' => $request->get('notes')
        ]);

        $articlesToPrint = new Collection();
        $quantities = collect($request->get('quantities'));
        $order->items->each(function ($orderItem) use ($quantities, $delivery, $order, $request, &$articlesToPrint) {
            /* @var OrderItem $orderItem */
            $quantity = intval($quantities->get($orderItem->article->id));
            if ($quantities->has($orderItem->article->id) && $quantity > 0) {
                $deliveryItem = $delivery->items()->create([
                    'article_id' => $orderItem->article->id,
                    'quantity' => $quantity
                ]);

                if ($request->get('print_label')) {
                    $articlesToPrint->push($orderItem->article);
                }

                $orderItem->article->changeQuantity($quantity, ArticleQuantityChangelog::TYPE_INCOMING, 'Bestellung '.$order->internal_order_number, $deliveryItem);
            }
        });

        if ($order->isFullyDelivered()) {
            $order->status = Order::STATUS_DELIVERED;
            $order->save();
        } else {
            $order->status = Order::STATUS_PARTIALLY_DELIVERED;
            $order->save();
        }

        if ($order->invoice_received) {
            User::first()->notify(new NewDeliverySaved($delivery));
        }

        if ($request->get('print_label') && $articlesToPrint->count() > 0) {
            $labelService = new PrintLabelService();
            $labelService->printArticleLabels($articlesToPrint);
        }

        return redirect()->route('order.show', $order);
    }

    protected function getArticleList() {
        return Article::active()->with('suppliers')->withCurrentSupplier()->withCurrentSupplierArticle()->orderBy('name')->get()
            ->transform(function ($article) {
                /*@var $article Article */
                return [
                    'id' => $article->id,
                    'name' => $article->name/*.(!empty($article->unit) ? ' ('.$article->unit->name.')' : '')*/,
                    'supplier_id' => $article->currentSupplier->id,
                    'category' => $article->category->name ?? '',
                    'order_quantity' => $article->currentSupplierArticle->order_quantity ?? 0,
                    'price' => $article->currentSupplierArticle->price ? $article->currentSupplierArticle->price / 100 : 0
                ];
            });
    }

    public function changePaymentStatus(Order $order, Request $request) {
        if (array_key_exists(intval($request->get('type')), Order::PAYMENT_STATUS_TEXT)) {
            $order->payment_status = intval($request->get('type'));
            $order->save();
            flash('Bezahlstatus geändert.', 'success');
        } else {
            flash('Bezahltstatus ungültig.', 'error');
        }

        return redirect()->route('order.show', $order);
    }
}
