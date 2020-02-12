<?php

namespace Mss\Models;

use Mss\Models\Traits\GetAudits;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

abstract class AuditableModel extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;
    use GetAudits;

    protected $auditsToDisplay = 20;
    protected $fieldNames = [];
    protected $ignoredAuditFields = ['id'];

    /**
     * @return string
     */
    abstract public static function getAuditName();

    /**
     * @return array
     */
    abstract public static function getFieldNames();
}