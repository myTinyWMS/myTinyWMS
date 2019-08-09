<?php

namespace Mss\Http\Controllers\Article;

use Carbon\Carbon;
use Mss\Models\Order;
use Mss\Models\Article;
use Illuminate\Http\Request;
use Mss\Http\Controllers\Controller;
use Mss\Models\ArticleQuantityChangelog;

class QuantityChangelogController extends Controller
{
    public function index(Article $article, Request $request) {
        $dateStart = $request->has('start') ? Carbon::parse($request->get('start')) : Carbon::now()->subMonth(12)->firstOfMonth();
        $dateEnd = $request->has('end') ? Carbon::parse($request->get('end'))->addDay() : Carbon::now();
        $changelog = $article->quantityChangelogs()->with(['user', 'unit', 'deliveryItem.delivery.order'])->latest()->whereBetween('created_at', [$dateStart, $dateEnd])->paginate(100);
        $diffMonths = abs($dateEnd->diffInMonths($dateStart));

        $chartLabels = collect([$dateStart->format('Y-m') => $dateStart->formatLocalized('%b %Y')]);
        for($i = 1; $i <= $diffMonths; $i++) {
            $chartLabels->put($dateStart->copy()->addMonth($i)->format('Y-m'), $dateStart->copy()->addMonth($i)->formatLocalized('%b %Y'));
        }

        $all = $article->quantityChangelogs()->oldest()->whereBetween('created_at', [$dateStart, $dateEnd])->whereIn('type', [ArticleQuantityChangelog::TYPE_OUTGOING, ArticleQuantityChangelog::TYPE_INCOMING, ArticleQuantityChangelog::TYPE_INVENTORY])->get();

        $chartValues = collect();
        $chartValues->put(1, collect());
        $chartValues->put(2, collect());

        $chartLabels->each(function ($label, $key) use (&$chartValues) {
            $chartValues[1]->put($key, 0);
            $chartValues[2]->put($key, 0);
        });

        $all->groupBy(function (&$item) {
            if ($item->type == ArticleQuantityChangelog::TYPE_INVENTORY) {
                $item->type = ArticleQuantityChangelog::TYPE_OUTGOING;
            }

            return $item->type;
        })->each(function ($group, $type) use ($chartLabels, &$chartValues) {
            $group->groupBy(function ($item) {
                return $item->created_at->format('Y-m');
            })->transform(function ($items) {
                return $items->sum('change');
            })->each(function ($value, $key) use (&$chartValues, $type) {
                $chartValues[$type]->put($key, $value);
            });
        });

        $chartValues->transform(function ($values) {
            return $values->values();
        });

        $chartLabels = $chartLabels->values();

        return view('article.quantity_changelog', compact('article', 'changelog', 'dateStart', 'dateEnd', 'chartLabels', 'chartValues', 'diffMonths'));
    }

    public function delete(Article $article, ArticleQuantityChangelog $changelog) {
        if ($changelog->deliveryItem) {
            $delivery = $changelog->deliveryItem->delivery;
            $changelog->deliveryItem->delete();

            if ($delivery && $delivery->items()->count() == 0) {
                $order = $delivery->order;
                $delivery->delete();

                if ($order->status == Order::STATUS_DELIVERED && $order->items()->count() > 1) {
                    $order->status = Order::STATUS_PARTIALLY_DELIVERED;
                    $order->save();
                }

                flash('Lieferung zur Bestellung '.link_to_route('order.show', $order->internal_order_number, $order).' gelöscht, da keine Artikel mehr vorhanden', 'warning');
            }
        }

        if (!in_array($changelog->type, [ArticleQuantityChangelog::TYPE_REPLACEMENT_DELIVERY, ArticleQuantityChangelog::TYPE_OUTSOURCING])) {
            $change = $changelog->change * -1;
            $article->quantity += $change;
        } elseif($changelog->type == ArticleQuantityChangelog::TYPE_OUTSOURCING) {
            $article->outsourcing_quantity += $changelog->change;
        } elseif($changelog->type == ArticleQuantityChangelog::TYPE_REPLACEMENT_DELIVERY) {
            $article->replacement_delivery_quantity += $changelog->change;
        }
        $article->save();

        $changelog->delete();

        flash('Bestandsänderung gelöscht')->success();

        return redirect()->route('article.show', $article);
    }

    public function changeChangelogNote(Request $request) {
        $changelog = ArticleQuantityChangelog::findOrFail($request->get('id'));
        $changelog->note = $request->get('content');
        $changelog->save();

        return response('', 200);
    }
}
