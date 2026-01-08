<?php

namespace Mss\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Mss\Mail\MinQuantitiesCalculation;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;

class CalculateMinQuantitiesCommand extends Command {
    protected $signature = 'calculate:min';

    protected $description = 'Calculate and update minimal quantities for articles based on recent usage';

    public function handle(): void {
        $items = Article::where('auto_min_quantity_duration', '>', 0)
            ->withCurrentSupplierArticle()
            ->get()
            ->map(function (Article $article) {
                return [
                    'article' => $article,
                    'new_quantity' => max(20, $this->processArticle($article))
                ];
            })
            ->filter(function ($item) {
                return $item['new_quantity'] !== null && $item['new_quantity'] > 0 && $item['article']->min_quantity != $item['new_quantity'];
            });

        if ($items->isEmpty()) {
            $this->output->note('no articles with auto min quantity duration found');
            return;
        }

        $to = explode(',', env('INVENTORY_MANUAL_RECEIVER'));
        Mail::to($to)->send(new MinQuantitiesCalculation($items));
    }

    protected function processArticle(Article $article): ?int
    {
        $duration = $this->getDurationForArticle($article);
        if ($duration === null) {
            return null;
        }

        $soldQuantity = abs($this->getSoldQuantity($article, $duration));
        if ($soldQuantity <= 0) {
            return null;
        }

        /*$deliveryTime = $this->getDeliveryTime($article);
        if ($deliveryTime <= 0) {
            return null;
        }*/

        $newMin = $this->calculateNewMinQuantity($soldQuantity, $duration, 7);

//        $this->applyNewMin($article, $newMin);

        return $newMin;
    }

    protected function getDurationForArticle(Article $article): ?int
    {
        switch ($article->auto_min_quantity_duration) {
            case Article::AUTO_MIN_QUANTITY_DURATION_7_DAYS:
                return 7;
            case Article::AUTO_MIN_QUANTITY_DURATION_14_DAYS:
                return 14;
            case Article::AUTO_MIN_QUANTITY_DURATION_30_DAYS:
                return 30;
            case Article::AUTO_MIN_QUANTITY_DURATION_60_DAYS:
                return 60;
            default:
                return null;
        }
    }

    protected function getSoldQuantity(Article $article, int $duration): int
    {
        return (int) $article
            ->quantityChangelogs()
            ->where('type', ArticleQuantityChangelog::TYPE_OUTGOING)
            ->where('created_at', '>', now()->subDays($duration))
            ->sum('change');
    }

    protected function getDeliveryTime(Article $article): int
    {
        $currentSupplierArticle = $article->currentSupplierArticle;
        return $currentSupplierArticle ? intval($currentSupplierArticle->delivery_time) : 0;
    }

    protected function calculateNewMinQuantity(int $soldQuantity, int $duration, int $deliveryTime): float
    {
        return ($duration > 0) ? ($soldQuantity / $duration) * $deliveryTime : 0.0;
    }

    protected function applyNewMin(Article $article, float $newMin): void
    {
        $this->output->note('setting new min quantity ' . $newMin . ' to article ' . $article->id . ' - old value: ' . $article->min_quantity);
    }
}
