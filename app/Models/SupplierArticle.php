<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

class SupplierArticle extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;

}
