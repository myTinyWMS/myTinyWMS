<?php

namespace Mss\Listeners;

use Illuminate\Support\Facades\Notification;
use Mss\Events\DeliverySaved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mss\Models\OrderItem;
use Mss\Models\UserSettings;
use Mss\Notifications\NewDeliverySavedAndInvoiceExists;
use Mss\Notifications\NewDeliverySavedForWatchedCategories;
use Mss\Notifications\NewDeliveryWithVaryingDeliveryQuantity;

class DeliverySavedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DeliverySaved  $event
     * @return void
     */
    public function handle(DeliverySaved $event)
    {
        $this->notifyIfInvoiceHasBeenReceived($event);
        $this->notifyIfCategoriesHaveBeenWatched($event);
        $this->notifyIfDeliveryQuantityDifferesFromOrderQuantity($event);
    }

    /**
     * @param DeliverySaved $event
     */
    protected function notifyIfCategoriesHaveBeenWatched(DeliverySaved $event) {
        $articles = $event->delivery->items->map(function ($item) {
            return $item->article;
        });

        UserSettings::getUsersWhereHas(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES)->each(function ($user) use ($articles, $event) {
            $watchedArticles = $articles->whereIn('category_id', $user->settings()->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES));
            if ($watchedArticles->count()) {
                Notification::send($user, new NewDeliverySavedForWatchedCategories($watchedArticles, $event->delivery));
            }
        });
    }

    protected function notifyIfInvoiceHasBeenReceived(DeliverySaved $event) {
        $invoiceReceivedForAtLeastOneItem = ($event->delivery->order->items->whereIn('invoice_received', [OrderItem::INVOICE_STATUS_RECEIVED, OrderItem::INVOICE_STATUS_CHECK])->count() > 0);
        if ($invoiceReceivedForAtLeastOneItem) {
            Notification::send(UserSettings::getUsersWhereTrue(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED), new NewDeliverySavedAndInvoiceExists($event->delivery));
        }
    }

    protected function notifyIfDeliveryQuantityDifferesFromOrderQuantity(DeliverySaved $event) {
        $itemsWithVaryingQuantities = $event->delivery->items->filter(function ($deliveryItem) {
            $orderItem = $deliveryItem->delivery->order->items->where('article_id', $deliveryItem->article_id)->first();
            return $orderItem->quantity != $deliveryItem->quantity;
        });

        if ($itemsWithVaryingQuantities->count()) {
            Notification::send(UserSettings::getUsersWhereTrue(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_DELIVERY_QUANTITY_DIFFERS_FROM_ORDER_QUANTITY), new NewDeliveryWithVaryingDeliveryQuantity($event->delivery));
        }
    }
}
