<?php

namespace Mss\Http\Controllers\Order;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Mss\Events\DeliverySaved;
use Mss\Http\Controllers\Controller;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Delivery;
use Mss\Models\DeliveryItem;
use Mss\Models\Order;
use Mss\Models\OrderItem;
use Mss\Services\PrintLabelService;

class DeliveryController extends Controller
{
    public function create(Order $order) {
        $order->load(['items.article' => function($query) {
            $query->withCurrentSupplierArticle();
        }]);

        return view('order.delivery_form', compact('order'));
    }

    public function store(Order $order, Request $request) {
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

        flash(__('Lieferung gespeichert.'))->success();

        return redirect()->route('order.show', $order);
    }

    public function delete(Order $order, Delivery $delivery) {
        $delivery->items->each(function ($deliveryItem) {
            /** @var DeliveryItem $deliveryItem */
            $deliveryItem->articleChangeLog->delete();
        });

        flash(__('Lieferung gelöscht'))->success();

        return redirect()->route('order.show', $order);
    }
}
