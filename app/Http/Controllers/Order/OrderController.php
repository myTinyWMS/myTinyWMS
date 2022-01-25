<?php

namespace Mss\Http\Controllers\Order;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mss\Models\Tag;
use Mss\Models\Order;
use Mss\Models\Article;
use Webpatser\Uuid\Uuid;
use Mss\Models\Supplier;
use Mss\Models\Category;
use Mss\Models\OrderItem;
use Illuminate\Http\Request;
use Mss\Models\OrderMessage;
use Mss\Models\UserSettings;
use Mss\DataTables\OrderDataTable;
use Mss\Http\Requests\OrderRequest;
use Mss\Http\Controllers\Controller;
use Mss\DataTables\AssignOrderDataTable;
use Mss\DataTables\SelectArticleDataTable;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(OrderDataTable $orderDataTable) {
        $this->authorize('order.view', Order::class);

        $unassignedMessages = OrderMessage::unassigned()->unread()->count();
        $assignedMessages = OrderMessage::assigned()->unread()->with('order.supplier')->get();
        $supplier = Supplier::orderedByName()->get();

        return $orderDataTable->render('order.list', compact('assignedMessages', 'unassignedMessages', 'supplier'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request, SelectArticleDataTable $selectArticleDataTable) {
        $this->authorize('order.create', Order::class);

        if (!$request->has('draw')) {
            $allArticles = $this->getArticleList();
            $categories = Category::orderedByName()->get();
            $tags = Tag::orderedByName()->get();

            $order = new Order();
            $order->internal_order_number = $order->getNextInternalOrderNumber();
            $order->order_date = Carbon::now();
            $order->save();

            $preSetArticles = collect();
            if ($request->has('article')) {
                $articles = Article::withCurrentSupplierArticle()->find($request->get('article'));
                $preSetArticles = ($articles instanceof Article) ? collect([$articles]) : $articles;
                $preSetArticles->transform(function ($article) {
                    $deliveryTime = intval($article->currentSupplierArticle->delivery_time);
                    $deliveryDate = Carbon::now()->addWeekdays($deliveryTime);

                    return [
                        'id' => $article->id,
                        'order_item_id' => null,
                        'name' => $article->name,
                        'supplier_id' => $article->currentSupplierArticle->supplier_id,
                        'order_notes' => $article->order_notes ?? '',
                        'price' => $article->currentSupplierArticle->price ? formatPriceValue($article->currentSupplierArticle->price / 100) : '',
                        'quantity' => $article->currentSupplierArticle->order_quantity ?? '',
                        'expected_delivery' => $deliveryDate->format('Y-m-d')
                    ];
                });
            }
        }

        return $selectArticleDataTable->render('order.create', compact('order', 'allArticles', 'tags', 'categories', 'preSetArticles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(OrderRequest $request) {
        $this->authorize('order.create', Order::class);

        /* @var $order Order */
        $order = Order::findOrFail($request->get('order_id'));

        $order->status = $request->get('status');
        $order->payment_status = $request->get('payment_status');
        $order->supplier_id = $request->get('supplier_id');
        $order->external_order_number = $request->get('external_order_number');
        $order->total_cost = parsePrice($request->get('total_cost'));
        $order->shipping_cost = parsePrice($request->get('shipping_cost')) ?? 0;
        $order->order_date = !empty($request->get('order_date')) ? Carbon::parse($request->get('order_date')) : null;
        $order->notes = $request->get('notes');

        if ($order->status === Order::STATUS_NEW) {
            $order->status = Order::STATUS_ORDERED;
        }

        $order->save();

        // save order items
        $existingItemIds = $order->items->pluck('id');
        $updatedItemIds = collect();
        collect(\GuzzleHttp\json_decode($request->get('article_data'), true))->each(function ($item) use ($order, $updatedItemIds) {
            if (!empty($item['order_item_id'])) {
                $orderItem = OrderItem::findOrFail($item['order_item_id']);
                $orderItem->price = parsePrice($item['price']);
                $orderItem->quantity = intval($item['quantity']);
                $orderItem->expected_delivery = !empty($item['expected_delivery']) ? Carbon::parse($item['expected_delivery']) : null;
                $orderItem->save();
                $updatedItemIds->push($orderItem->id);
            } else {
                $order->items()->create([
                    'article_id' => $item['id'],
                    'price' => parsePrice($item['price']),
                    'quantity' => $item['quantity'],
                    'expected_delivery' => !empty($item['expected_delivery']) ? Carbon::parse($item['expected_delivery']) : null
                ]);
            }
        });

        $missingItemIds = $existingItemIds->diff($updatedItemIds);
        OrderItem::whereIn('id', $missingItemIds)->delete();

        flash(__('Bestellung gespeichert'), 'success');
        return redirect()->route('order.show', $order);
    }

    /**
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancel(Order $order) {
        $this->authorize('order.delete', Order::class);

        $order->delete();

        return redirect()->route('order.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @param AssignOrderDataTable $assignOrderDataTable
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id, AssignOrderDataTable $assignOrderDataTable) {
        $this->authorize('order.view', Order::class);

        $order = Order::with('items.order.items')->findOrFail($id);
        $audits = $order->getAllAudits();
        $messages = $order->messages()->with('user')->latest('received')->get();
        $invoiceNotificationUsersCount = UserSettings::getUsersWhereTrue(UserSettings::SETTING_NOTIFY_ON_INVOICE_CHECKS)->count();
        $hasOneArticleWithNewPrice = $order->items->filter(function ($item) {
            return (($item->article->getCurrentSupplierArticle()->price / 100) != $item->price);
        })->count() > 0;

        return $assignOrderDataTable->render('order.show', compact('order', 'audits', 'messages', 'hasOneArticleWithNewPrice', 'invoiceNotificationUsersCount'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id, SelectArticleDataTable $selectArticleDataTable) {
        $this->authorize('order.edit', Order::class);

        $allArticles = $this->getArticleList();
        $order = Order::with(['items.article' => function($query) {
            $query->withCurrentSupplierArticle();
        }])->findOrFail($id);
        $categories = Category::orderedByName()->get();
        $tags = Tag::orderedByName()->get();

        /* @var $order Order */
        $preSetArticles = $order->items;
        $preSetArticles->transform(function ($item) use ($order) {
            return [
                'id' => $item->article->id,
                'order_item_id' => $item->id,
                'name' => $item->article->name,
                'supplier_id' => $item->article->getSupplierArticleAtDate($order->created_at, false)->supplier_id,
                'order_notes' => $item->article->order_notes ?? '',
                'price' => formatPriceValue($item->price),
                'quantity' => $item->quantity,
                'expected_delivery' => optional($item->expected_delivery)->format('Y-m-d')
            ];
        });

        return $selectArticleDataTable->render('order.edit', compact('order', 'preSetArticles', 'allArticles', 'tags', 'categories'));
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
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id) {
        $this->authorize('order.delete', Order::class);

        $order = Order::findOrFail($id);

        if ($order->messages()->count() > 0 || $order->deliveries()->count() > 0) {
            flash(__('Bestellung kann nicht gelöscht werden, sie enthält bereits Nachrichten und/oder Lieferungen!'), 'danger');
            return redirect()->route('order.index');
        }

        $order->items()->delete();
        $order->messages()->delete();
        $order->delete();

        flash(__('Bestellung gelöscht'), 'success');
        return redirect()->route('order.index');
    }

    /**
     * @return Collection
     */
    protected function getArticleList() {
        return Article::enabled()->with(['suppliers', 'category'])->withCurrentSupplier()->withCurrentSupplierArticle()->orderBy('name')->get()
            ->filter(function ($article) {
                return !empty($article->currentSupplierArticle);
            })
            ->transform(function ($article) {
                /*@var $article Article */
                $deliveryTime = intval($article->currentSupplierArticle->delivery_time);
                $deliveryDate = Carbon::now()->addWeekdays($deliveryTime);
                return [
                    'id' => $article->id,
                    'name' => $article->name/*.(!empty($article->unit) ? ' ('.$article->unit->name.')' : '')*/,
                    'supplier_id' => $article->currentSupplier->id,
                    'category' => $article->category->name ?? '',
                    'order_notes' => $article->order_notes ?? '',
                    'delivery_date' =>  $deliveryDate->format('Y-m-d'),
                    'order_quantity' => $article->currentSupplierArticle->order_quantity ?? '',
                    'price' => $article->currentSupplierArticle->price ? formatPriceValue($article->currentSupplierArticle->price / 100) : ''
                ];
            });
    }

    /**
     * @param Order $order
     * @param $payment_status
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function changePaymentStatus(Order $order, $payment_status) {
        $this->authorize('order.edit', Order::class);

        if (array_key_exists(intval($payment_status), Order::getPaymentStatusText())) {
            $order->payment_status = intval($payment_status);
            $order->save();
            flash(__('Bezahlstatus geändert.'), 'success');
        } else {
            flash(__('Bezahltstatus ungültig.'), 'error');
        }

        return redirect()->route('order.show', $order);
    }

    /**
     * @param Order $order
     * @param $status
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function changeStatus(Order $order, $status) {
        $this->authorize('order.edit', Order::class);

        if (array_key_exists(intval($status), Order::getStatusTexts())) {
            $order->status = intval($status);
            $order->save();
            flash(__('Status geändert.'), 'success');
        } else {
            flash(__('Status ungültig.'), 'error');
        }

        return redirect()->route('order.show', $order);
    }

    /**
     * @param Order $order
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function uploadInvoiceCheckAttachments(Order $order, Request $request) {
        if (config('app.demo')) {
            return response()->json('error', 400);
        }

        $this->authorize('order.edit', Order::class);

        $file = $request->file('file');

        /**
         * @todo queue file to delete after some time
         */
        $upload_success = $file->storeAs('upload_temp', $order->id.'_'.Uuid::generate(4)->string);

        if ($upload_success) {
            return response()->json($upload_success, 200);
        } else {
            return response()->json('error', 400);
        }
    }

    public function setInvoiceNumber(Order $order, Request $request) {
        $order->external_invoice_number = $request->get('external_invoice_number');
        $order->save();

        flash(__('Rechnungsnummer gespeichert'))->success();

        return redirect()->route('order.show', $order);
    }
}
