<?php

namespace Mss\Services;

use Carbon\Carbon;
use Mss\Models\Delivery;
use Illuminate\Database\Eloquent\Collection;

class ReportService {
    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param null|integer $category
     * @return Collection
     */
    public function getInvoicesWithDeliveries($start, $end, $category = null) {
        $items = Delivery::whereBetween('delivery_date', [$start, $end])
            ->whereHas('items.orderItem', function ($query) {
                $query->where('invoice_received', 1);
            })
            ->with(['items.article', 'items.orderItem', 'order.supplier'])
            ->get();

        if (!empty($category)) {
            $items = $items->transform(function ($delivery) use ($category) {
                $items = $delivery->items;
                $items = $items->filter(function ($deliveryItem) use ($category) {
                    return $deliveryItem->article->category_id == $category;
                });
                $delivery->items = $items;
                return $delivery;
            })->filter(function ($delivery) {
                return $delivery->items->count() > 0;
            });
        }

        $items = $items->groupBy('order_id')->sortBy(function ($items) {
            return $items->first()->order->internal_order_number;
        });

        return $items;
    }
}