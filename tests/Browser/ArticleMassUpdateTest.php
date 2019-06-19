<?php

namespace Tests\Browser;

use Mss\Models\Article;
use Mss\Models\Category;
use Mss\Models\Supplier;
use Mss\Models\Unit;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ArticleMassUpdateTest extends DuskTestCase
{
    /**
     * login before all other tests
     *
     * @throws \Throwable
     */
    public function test_login() {
        $this->browse(function (Browser $browser) {
            $browser->resize(1920, 2000);
            $this->login($browser);
        });
    }

    public function test_article_mass_update() {
        $this->browse(function (Browser $browser) {
            $articles = Article::active()->inRandomOrder()->take(5)->get();
            
            $article1 = $articles->get(0);
            $article2 = $articles->get(1);
            $article3 = $articles->get(2);
            $article4 = $articles->get(3);
            $article5 = $articles->get(4);
            
            $inventory1 = $article1->inventory == Article::INVENTORY_TYPE_SPARE_PARTS ? Article::INVENTORY_TYPE_CONSUMABLES : Article::INVENTORY_TYPE_SPARE_PARTS;
            $inventory2 = $article2->inventory == Article::INVENTORY_TYPE_SPARE_PARTS ? Article::INVENTORY_TYPE_CONSUMABLES : Article::INVENTORY_TYPE_SPARE_PARTS;
            $inventory3 = $article3->inventory == Article::INVENTORY_TYPE_SPARE_PARTS ? Article::INVENTORY_TYPE_CONSUMABLES : Article::INVENTORY_TYPE_SPARE_PARTS;
            $inventory4 = $article4->inventory == Article::INVENTORY_TYPE_SPARE_PARTS ? Article::INVENTORY_TYPE_CONSUMABLES : Article::INVENTORY_TYPE_SPARE_PARTS;
            $inventory5 = $article5->inventory == Article::INVENTORY_TYPE_SPARE_PARTS ? Article::INVENTORY_TYPE_CONSUMABLES : Article::INVENTORY_TYPE_SPARE_PARTS;

            // make unit empty to set it via mass update
            $article4->unit_id = null;
            $article4->save();
            $article5->unit_id = null;
            $article5->save();
            
            $unit1 = Unit::inRandomOrder()->first();
            $unit2 = Unit::inRandomOrder()->first();

            $browser->visit('/article/mass-update')
                ->waitForText('Speichern')
                ->select('#inventory_'.$article1->id, $inventory1)
                ->select('#inventory_'.$article2->id, $inventory2)
                ->select('#inventory_'.$article3->id, $inventory3)
                ->select('#inventory_'.$article4->id, $inventory4)
                ->select('#inventory_'.$article5->id, $inventory5)
                ->select('#unit_'.$article4->id, $unit1->id)
                ->select('#unit_'.$article5->id, $unit2->id)                
                ->click('#submit')
                ->waitForText('Änderungen gespeichert');
            
            $article1->refresh();
            $article2->refresh();
            $article3->refresh();
            $article4->refresh();
            $article5->refresh();

            $this->assertEquals($inventory1, $article1->inventory);
            $this->assertEquals($inventory2, $article2->inventory);
            $this->assertEquals($inventory3, $article3->inventory);
            $this->assertEquals($inventory4, $article4->inventory);
            $this->assertEquals($inventory5, $article5->inventory);

            $this->assertEquals($unit1->id, $article4->unit_id);
            $this->assertEquals($unit2->id, $article5->unit_id);
        });
    }
}