<?php

namespace Mss\Models\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;

trait GetAudits {

    /**
     * @return Collection
     */
    public function getAudits() {
        $audits = $this->audits()->with('user')->orderBy('created_at', 'desc')->take($this->auditsToDisplay ?? 20)->get();
        $audits->transform(function ($audit) {
            $metaData = $audit->getMetadata();
            $modified = collect($audit->getModified())
                ->forget($this->ignoredAuditFields)
                ->transform(function ($modified, $field) {
                    $modified['name'] = $this->fieldNames[$field] ?? $field;

                    if (array_key_exists($field, $this->getAuditFormatters()) && is_callable($this->getAuditFormatters()[$field])) {
                        if (array_key_exists('old', $modified)) {
                            $modified['old'] = $this->getAuditFormatters()[$field]($modified['old']);
                        }

                        if (array_key_exists('new', $modified)) {
                            $modified['new'] = $this->getAuditFormatters()[$field]($modified['new']);
                        }
                    }

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

    /**
     * @return array
     */
    protected function getAuditFormatters() {
        return [];
    }
}