<?php

namespace Mss\Models;

use Mss\Exceptions\InvalidParameterException;

class UserSettings {

    /**
     * @var User
     */
    protected $user;

    const SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED = 'notify_after_new_delivery_if_invoice_received';
    const SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES = 'notify_after_new_delivery_in_those_categories';

    /**
     * array
     */
    const DEFAULTS = [
        [
            'key' => self::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED,
            'default' => false,
            'type' => 'boolean'
        ],
        [
            'key' => self::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES,
            'default' => [],
            'type' => 'array'
        ]
    ];

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function get($key) {

    }

    /**
     * @param array $attributes
     * @return bool
     */
    public function merge(array $attributes) {
        $settings = array_merge($this->user->settings, array_only($attributes, collect(self::DEFAULTS)->pluck('key')));

        return $this->user->update(compact('settings'));
    }

    /*protected function mergeWithDefault(array $attributes) {
        $settings = collect(self::DEFAULTS);
        collect($attributes)->each(function ($key, $value) use (&$settings, $attributes) {
            $setting = $settings->where('key', $key);
            if (!$setting) {
                throw new InvalidParameterException('Invalid Setting Key "'.$key.'"', $attributes);
            }

            if (gettype($value) !== $setting['type']) {
                throw new InvalidParameterException('Invalid Setting Type for Key "'.$key.'"', $attributes);
            }


        });

        return $settings;
    }*/
}