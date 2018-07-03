<?php

namespace Mss\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Mss\Models\Traits\GetAudits;
use OwenIt\Auditing\Contracts\Auditable;

class AuditableModel extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;
    use GetAudits;

    protected $auditsToDisplay = 20;
    protected $fieldNames = [];
    protected $ignoredAuditFields = ['id'];

    /**
     * @param string $attribute
     * @param Carbon|string $date
     * @return mixed|null
     */
    public function getAttributeAtDate($attribute, $date) {
        $date = ($date instanceof Carbon) ? $date : Carbon::parse($date);

        /* @var $audits \Illuminate\Support\Collection */
        $audits = $this->audits->filter(function ($audit) use ($attribute) {
            return array_key_exists($attribute, $audit->getModified());
        })->sortBy('created_at');

        // model didn't exists before requested date
        if ($date->lt($this->created_at)) {
            return null;
        }

        // no changes to after requested date, use current value
        if ($date->gt($audits->max('created_at'))) {
            return $this->retransformAudits($attribute, $this->{$attribute});
        }

        // search for first change after requested date, use old value
        $firstChangeAfterDate = $audits->firstWhere('created_at', '>', $date);
        if ($firstChangeAfterDate) {
            return $this->retransformAudits($attribute, $firstChangeAfterDate->getModified()[$attribute]['old']);
        }

        Log::error('No Audit found', compact('audits'));
        return null;
    }

    /**
     * @return array
     */
    protected function getAuditMappings() {
        return [];
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return int|string
     */
    protected function retransformAudits($attribute, $value) {
        if (array_key_exists($attribute, $this->getAuditMappings())) {
            foreach ($this->getAuditMappings()[$attribute] as $realValue => $mappings) {
                if (in_array($value, $mappings, true)) {
                    return $realValue;
                }
            }
        }

        return $value;
    }
}