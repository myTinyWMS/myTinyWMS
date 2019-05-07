<?php

namespace Mss\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Mss\DataTables\ArticleDataTable;
use Mss\DataTables\AssignOrderDataTable;
use Mss\DataTables\OrderDataTable;
use Mss\DataTables\SelectArticleDataTable;
use Mss\Events\DeliverySaved;
use Mss\Http\Requests\OrderRequest;
use Mss\Mail\InvoiceCheckMail;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Category;
use Mss\Models\Delivery;
use Mss\Models\Order;
use Mss\Models\OrderItem;
use Mss\Models\OrderMessage;
use Mss\Models\Supplier;
use Mss\Models\Tag;
use Mss\Models\User;
use Mss\Models\UserSettings;
use Mss\Notifications\NewDeliverySavedAndInvoiceExists;
use Mss\Services\PrintLabelService;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(OrderDataTable $orderDataTable) {
        $unassignedMessages = OrderMessage::unassigned()->unread()->count();
        $assignedMessages = OrderMessage::assigned()->unread()->with('order.supplier')->get();
        $supplier = Supplier::orderedByName()->get();

        return $orderDataTable->render('order.list', compact('assignedMessages', 'unassignedMessages', 'supplier'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, SelectArticleDataTable $selectArticleDataTable) {
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request) {
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

        flash('Bestellung gespeichert', 'success');
        return redirect()->route('order.show', $order);
    }

    public function itemConfirmationReceived(OrderItem $orderitem) {
        $orderitem->confirmation_received = true;
        $orderitem->save();

        return redirect()->route('order.show', $orderitem->order);
    }

    public function itemInvoiceReceived(OrderItem $orderitem, Request $request) {
        $request->validate([
            'invoice_status' => 'required|in:0,1,2',
            'change_article_price' => 'in:0,1'
        ]);

        $orderitem->invoice_received = $request->get('invoice_status');
        $orderitem->save();

        if (request('change_article_price') == 1) {
            $supplierArticle = $orderitem->article->getCurrentSupplierArticle();
            $supplierArticle->price = $orderitem->price * 100;
            $supplierArticle->save();
        }

        if (!empty($request->get('mail_note'))) {
            $attachments = collect(json_decode($request->get('mail_attachments'), true));
            Mail::to(UserSettings::getUsersWhereTrue(UserSettings::SETTING_NOTIFY_ON_INVOICE_CHECKS))->send(new InvoiceCheckMail($orderitem->order, $request->get('mail_note'), $attachments));
        }

        return redirect()->route('order.show', $orderitem->order);
    }

    public function allItemsInvoiceReceived(Order $order) {
        $order->items->each(function ($orderitem) {
            if ($orderitem->invoice_received !== OrderItem::INVOICE_STATUS_RECEIVED) {
                $orderitem->invoice_received = OrderItem::INVOICE_STATUS_RECEIVED;
                $orderitem->save();
            }

            if (request('change_article_price') == 1) {
                $supplierArticle = $orderitem->article->getCurrentSupplierArticle();
                $supplierArticle->price = $orderitem->price * 100;
                $supplierArticle->save();
            }
        });

        return redirect()->route('order.show', $order);
    }

    public function allItemsConfirmationReceived(Order $order) {
        $order->items->each(function ($orderitem) {
            if (!$orderitem->confirmation_received) {
                $orderitem->confirmation_received = true;
                $orderitem->save();
            }
        });

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
        $audits = $order->getAllAudits();
        $messages = $order->messages()->with('user')->latest('received')->get();

        return $assignOrderDataTable->render('order.show', compact('order', 'audits', 'messages'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, SelectArticleDataTable $selectArticleDataTable) {
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
                'expected_delivery' => optional($item->expected_delivery)->format('d.m.Y')
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        flash('Bestellung gelöscht', 'success');
        return redirect()->route('order.index');
    }

    public function articleList(Supplier $supplier) {
        return response()->json($supplier->articles->pluck(['name', 'id']));
    }

    public function createDelivery(Order $order) {
        $order->load(['items.article' => function($query) {
            $query->withCurrentSupplierArticle();
        }]);

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

                if (array_key_exists($orderItem->article->id, $request->get('label_count', [])) && intval($request->get('label_count', [])[$orderItem->article->id]) > 0) {
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

        event(new DeliverySaved($delivery));

        if ($articlesToPrint->count() > 0) {
            $labelService = new PrintLabelService();
            $articlesToPrint->each(function ($article) use ($request, $labelService) {
                $count = intval($request->get('label_count', [])[$article->id]);
                for($i=1; $i<=$count; $i++) {
                    $labelService->printArticleLabels(new Collection([$article]), $request->get('label_type', [$article->id => 'small'])[$article->id]);
                }
            });
        }

        return redirect()->route('order.show', $order);
    }

    protected function getArticleList() {
        return Article::active()->with(['suppliers', 'category'])->withCurrentSupplier()->withCurrentSupplierArticle()->orderBy('name')->get()
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

    public function changeStatus(Order $order, Request $request) {
        if (array_key_exists(intval($request->get('status')), Order::STATUS_TEXTS)) {
            $order->status = intval($request->get('status'));
            $order->save();
            flash('Status geändert.', 'success');
        } else {
            flash('Status ungültig.', 'error');
        }

        return redirect()->route('order.show', $order);
    }

    public function uploadInvoiceCheckAttachments(Order $order, Request $request) {
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
}
