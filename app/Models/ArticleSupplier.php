<?php

namespace Mss\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

class ArticleSupplier extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * @return string
     */
    public function getUpdatedAtColumn() {
        if ($this->pivotParent) {
            return $this->pivotParent->getUpdatedAtColumn();
        }

        return static::UPDATED_AT;
    }
}
