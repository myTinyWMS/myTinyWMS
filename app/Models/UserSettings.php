<?php

namespace Mss\Models;

use Illuminate\Support\Collection;
use Mss\Exceptions\InvalidParameterException;

class UserSettings {

    /**
     * @var User
     */
    protected $user;

    const SETTING_NOTIFY_ABOUT_CORRECTION_ON_CHANGE_OF_OTHER_MONTH = 'notify_about_correction_on_change_of_other_month';
    const SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED = 'notify_after_new_delivery_if_invoice_received';
    const SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES = 'notify_after_new_delivery_in_those_categories';
    const SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_DELIVERY_QUANTITY_DIFFERS_FROM_ORDER_QUANTITY = 'notify_after_new_delivery_if_delivery_quantity_differs_from_order_quantity';
    const SETTING_NOTIFY_ON_INVOICE_CHECKS = 'notify_on_invoice_checks';
    const SETTINGS_HANDSCANNER_PIN_CODE = 'handscanner_pin_code';

    /**
     * array
     */
    const SETTINGS = [
        self::SETTING_NOTIFY_ABOUT_CORRECTION_ON_CHANGE_OF_OTHER_MONTH => [
            'default' => false,
            'type' => 'boolean'
        ],
        self::SETTING_NOTIFY_ON_INVOICE_CHECKS => [
            'default' => false,
            'type' => 'boolean'
        ],
        self::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED => [
            'default' => false,
            'type' => 'boolean'
        ],
        self::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_DELIVERY_QUANTITY_DIFFERS_FROM_ORDER_QUANTITY => [
            'default' => false,
            'type' => 'boolean'
        ],
        self::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES => [
            'default' => [],
            'type' => 'array'
        ],
        self::SETTINGS_HANDSCANNER_PIN_CODE => [
            'default' => null,
            'type' => 'string'
        ]
    ];

    /**
     * UserSettings constructor.
     *
     * @param User $user
     */
    public function __construct(User $user) {
        $this->user = $user;

        if (is_null($this->user->settings)) {
            $this->user->settings = [];
        }
    }

    /**
     * @param $key
     * @return mixed
     * @throws InvalidParameterException
     */
    public function get($key) {
        if (!array_key_exists($key, self::SETTINGS)) {
            throw new InvalidParameterException('Invalid Setting Key "'.$key.'"');
        }

        if (!array_key_exists($key, $this->user->settings)) {
            return self::SETTINGS[$key]['default'];
        }

        $value = $this->user->settings[$key];
        settype($value, self::SETTINGS[$key]['type']);

        return $value;
    }

    /**
     * @param $key
     * @param $value
     * @param bool $save
     * @return $this
     * @throws InvalidParameterException
     */
    public function set($key, $value, $save = false) {
        if (!array_key_exists($key, $this->user->settings) && !array_key_exists($key, self::SETTINGS)) {
            throw new InvalidParameterException('Invalid Setting Key "'.$key.'"');
        }

        settype($value, self::SETTINGS[$key]['type']);
        $settings = $this->user->settings;
        $settings[$key] = $value;
        $this->user->settings = $settings;

        if ($save) {
            $this->user->save();
        }

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function merge(array $attributes) {
        collect($attributes)->each(function ($value, $key) {
            $this->set($key, $value);
        });

        $this->user->save();

        return $this;
    }

    /**
     * @param $key
     * @return Collection
     */
    public static function getUsersWhereTrue($key) {
        return User::all()->filter(function ($user) use ($key) {
            return $user->settings()->get($key);
        });
    }

    /**
     * @param $key
     * @return Collection
     */
    public static function getUsersWhereHas($key) {
        return User::all()->filter(function ($user) use ($key) {
            return !empty($user->settings()->get($key));
        });
    }
}