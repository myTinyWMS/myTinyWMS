<?php

namespace Mss\Models\Legacy;

use Illuminate\Database\Eloquent\Model;

/**
 * Mss\Model\ORM\Categoriy
 *
 * @property integer $id
 * @property string $name
 * @property integer $bemerkung
 * @property string $anzahl
 */
class Category extends Model {
    protected $table = "category";
    protected $connection = 'onp';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];
}