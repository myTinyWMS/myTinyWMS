<?php

namespace Mss\Services;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Mss\Models\Article;
use Mss\Models\ArticleNote;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Category;
use Mss\Models\Legacy\Category as LegacyCategory;
use Mss\Models\Legacy\Supplier as LegacySupplier;
use Mss\Models\Legacy\Material as LegacyArticle;
use Mss\Models\Legacy\MaterialLog as LegacyArticleLog;
use Mss\Models\Supplier;
use Mss\Models\SupplierArticle;
use Mss\Models\Tag;
use Mss\Models\Unit;
use Mss\Models\User;

class ImportFromOnpService {

    protected $command;

    public function __construct(Command $command) {
        $this->command = $command;
        Category::unguard();
        Supplier::unguard();
        Article::unguard();
        SupplierArticle::unguard();
        Tag::unguard();
        ArticleQuantityChangelog::unguard();
        ArticleNote::unguard();
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

    public function importLog() {
        $this->command->info('Importing Article Log');
        $bar = $this->command->getOutput()->createProgressBar(LegacyArticleLog::count());

        $articleCache = [];
        $userCache = [];

        LegacyArticleLog::chunk(100, function ($items) use ($bar,$articleCache, $userCache) {
            foreach ($items as $log) {
                /* @var $log LegacyArticleLog */

                if (array_key_exists($log->material_id, $articleCache)) {
                    $article = $articleCache[$log->material_id];
                } else {
                    $article = $articleCache[$log->material_id] = Article::find($log->material_id);
                }

                if (!$article) {
                    continue;
                }

                if (array_key_exists($log->user_name, $userCache)) {
                    $user = $userCache[$log->user_name];
                } else {
                    $user = $userCache[$log->user_name] = User::firstOrCreate([
                        'name' => $log->user_name
                    ], [
                        'email' => $log->user_name,
                        'password' => bcrypt('password')
                    ]);
                }

                if ($log->type == 6) {
                    $article->articleNotes()->create([
                        'user_id' => $user->id,
                        'content' => $log->comment
                    ]);
                } else {
                    $article->quantityChangelogs()->create([
                        'type' => $log->type,
                        'user_id' => $user->id,
                        'created_at' => Carbon::parse($log->time_stamp),
                        'updated_at' => Carbon::parse($log->time_stamp),
                        'change' => ($log->type == ArticleQuantityChangelog::TYPE_OUTGOING ? (-1 * $log->count) : $log->count),
                        'new_quantity' => $log->ist_count,
                        'note' => $log->comment
                    ]);
                }
                $bar->advance();
            }
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

            $newArticle->addTag($article->maschinenzugehoerigkeit);

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