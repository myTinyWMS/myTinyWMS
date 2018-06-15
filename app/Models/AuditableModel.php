<?php

namespace Mss\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Mss\Models\Traits\GetAudits;
use OwenIt\Auditing\Contracts\Auditable;

class AuditableModel extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;
    use GetAudits;

    protected $auditsToDisplay = 20;
    protected $fieldNames = [];
    protected $ignoredAuditFields = ['id'];
}