<?php

namespace Mss\Models\Legacy;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Mss\Model\ORM\MaterialLog
 *
 * @property integer $id
 * @property string $user_name
 * @property Carbon $time_stamp
 * @property integer $type
 * @property integer $material_id
 * @property integer $count
 * @property integer $ist_count
 * @property string $comment
 * @property integer $status
 */
class MaterialLog extends Model {
    protected $table = "material_log";
    protected $connection = 'onp';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];
}