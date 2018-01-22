<?php

namespace Mss\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AuditableModel extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $auditsToDisplay = 20;
    protected $fieldNames = [];

    public function getAudits() {
        $audits = $this->audits()->orderBy('created_at', 'desc')->take($this->auditsToDisplay)->get();
        $audits->transform(function ($audit) {
            $metaData = $audit->getMetadata();
            $modified = collect($audit->getModified())
                ->forget('id')
                ->transform(function ($modified, $field) {
                    $modified['name'] = $this->fieldNames[$field] ?? $field;
                    return $modified;
                });

            return [
                'timestamp' => Carbon::parse($metaData['audit_created_at']),
                'user' => $audit->user->name,
                'modified' => $modified
            ];
        });

        return $audits;
    }
}