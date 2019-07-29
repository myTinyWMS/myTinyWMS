<?php

namespace Mss\Models\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mss\Models\Article;

trait GetAudits {

    /**
     * @return Collection
     */
    public function getAudits() {
        $audits = $this->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->take($this->auditsToDisplay ?? 20)->get();
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
                'user' => optional($audit->user)->name,
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

    /**
     * @param string $attribute
     * @param Carbon|string $date
     * @return mixed|null
     */
    public function getAttributeAtDate($attribute, $date) {
        $date = ($date instanceof Carbon) ? $date->endOfDay() : Carbon::parse($date)->endOfDay();

        /* @var $audits \Illuminate\Support\Collection */
        $audits = $this->audits->filter(function ($audit) use ($attribute) {
            return array_key_exists($attribute, $audit->getModified());
        })->sortBy('created_at');

        // model didn't exists before requested date
        $ignoreArticleCreatedDate = (!empty(env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT')) && $this->id <= env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT') && $this instanceof Article);
        $ignoreRelatedArticleCreatedAt = ($this->article && $this->article instanceof Article && !empty(env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT')) && $this->article->id <= env('LAST_ARTICLE_ID_CREATED_ON_FIRST_IMPORT'));
        if (!$ignoreArticleCreatedDate && !$ignoreRelatedArticleCreatedAt && $date->lt($this->created_at)) {
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