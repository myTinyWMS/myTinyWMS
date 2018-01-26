<?php

namespace Mss\Services;

use Illuminate\Console\Command;
use Mss\Models\Article;
use Mss\Models\Category;
use Mss\Models\Legacy\Category as LegacyCategory;
use Mss\Models\Legacy\Supplier as LegacySupplier;
use Mss\Models\Legacy\Material as LegacyArticle;
use Mss\Models\Supplier;
use Mss\Models\SupplierArticle;
use Mss\Models\Unit;

class ImportFromOnpService {

    protected $command;

    public function __construct(Command $command) {
        $this->command = $command;
        Category::unguard();
        Supplier::unguard();
        Article::unguard();
        SupplierArticle::unguard();
    }

    public function importCategories() {
        $this->command->info('Importing Categories');
        $bar = $this->command->getOutput()->createProgressBar(LegacyCategory::count());
        LegacyCategory::all()->each(function ($category) use ($bar) {
            /* @var $category LegacyCategory */
            Category::create([
                'id' => $category->id,
                'name' => $category->name,
                'notes' => $category->bemerkung
            ]);
            $bar->advance();
        });
        $bar->finish();
        $this->command->info(PHP_EOL);
    }

    public function importSuppliers() {
        $this->command->info('Importing Suppliers');
        $bar = $this->command->getOutput()->createProgressBar(LegacySupplier::count());
        LegacySupplier::all()->each(function ($supplier) use ($bar) {
            /* @var $supplier LegacySupplier */
            Supplier::create([
                'id' => $supplier->id,
                'name' => $supplier->company_name,
                'email' => $supplier->email,
                'phone' => $supplier->phone,
                'contact_person' => $supplier->contact_person,
                'website' => $supplier->website,
                'notes' => $supplier->comment
            ]);
            $bar->advance();
        });
        $bar->finish();
        $this->command->info(PHP_EOL);
    }

    public function importArticles() {
        $this->command->info('Importing Articles');
        $bar = $this->command->getOutput()->createProgressBar(LegacyArticle::count());
        LegacyArticle::all()->each(function ($article) use ($bar) {
            /* @var $article LegacyArticle */
            /* @var $newArticle Article */
            $unit = Unit::find($article->einheit + 1);
            $newArticle = Article::create([
                'id' => $article->id,
                'name' => $article->artikelbezeichnung,
                'quantity' => $article->bestand,
                'min_quantity' => $article->mindbestand,
                'usage_quantity' => $article->verbrauch,
                'notes' => $article->bemerkung,
                'status' => $article->status,
                'issue_quantity' => $article->entnahmemenge,
                'sort_id' => $article->sort_id,
                'inventory' => $article->inventur,
                'unit_id' => $unit ? $unit->id : null,
            ]);

            /**
             * $article->maschinenzugehoerigkeit -> Tag?
             */

            $newArticle->suppliers()->attach($article->hersteller, [
                'order_number' => $article->bestnr,
                'price' => $article->preis,
                'order_quantity' => $article->bestellmenge,
                'delivery_time' => $article->lieferzeit,
            ]);

            $newArticle->categories()->attach($article->type);

            $bar->advance();
        });
        $bar->finish();
        $this->command->info(PHP_EOL);
    }
}