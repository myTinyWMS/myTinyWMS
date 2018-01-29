<?php

namespace Mss\Models\Traits;

use Mss\Models\Tag;

trait Taggable {

    public function addTag($tag) {
        $tag = Tag::firstOrCreate(['name' => $tag]);

        $this->tags()->attach($tag);
    }

}