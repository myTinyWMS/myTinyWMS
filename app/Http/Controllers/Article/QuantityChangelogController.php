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
        /** @var Carbon $dataStartDate */
        /** @var Carbon $dataEndDate */
        $dataStartDate = $all->min('created_at');
        $dataEndDate = $all->max('created_at');
        $dataDiffInMonths = $dataEndDate ? $dataEndDate->diffInMonths($dataStartDate) + 1 : 0;

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

        return view('article.quantity_changelog', compact('article', 'changelog', 'dateStart', 'dateEnd', 'chartLabels', 'chartValues', 'dataDiffInMonths'));
    }

    public function delete(Article $article, ArticleQuantityChangelog $changelog) {
        $changelog->delete();

        flash(__('Bestandsänderung gelöscht'))->success();

        return redirect()->route('article.show', $article);
    }

    public function changeChangelogNote(Request $request) {
        $changelog = ArticleQuantityChangelog::findOrFail($request->get('id'));
        $changelog->note = $request->get('content');
        $changelog->save();

        return response('', 200);
    }
}
