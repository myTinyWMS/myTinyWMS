<?php

namespace Mss\Listeners;

use Illuminate\Support\Facades\Notification;
use Mss\Events\DeliverySaved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mss\Models\UserSettings;
use Mss\Notifications\NewDeliverySavedAndInvoiceExists;
use Mss\Notifications\NewDeliverySavedForWatchedCategories;

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
                Notification::send(UserSettings::getUsersWhereHas(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES), new NewDeliverySavedForWatchedCategories($watchedArticles, $event->delivery));
            }
        });
    }

    protected function notifyIfInvoiceHasBeenReceived(DeliverySaved $event) {
        $invoiceReceivedForAtLeastOneItem = ($event->delivery->order->items->where('invoice_received', true)->count() > 0);
        if ($invoiceReceivedForAtLeastOneItem) {
            Notification::send(UserSettings::getUsersWhereTrue(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED), new NewDeliverySavedAndInvoiceExists($event->delivery));
        }
    }
}
