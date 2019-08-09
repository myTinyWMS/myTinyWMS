<?php

namespace Tests\Unit\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Mss\Exceptions\InvalidParameterException;
use Mss\Models\User;
use Mss\Models\UserSettings;
use Mss\Services\ImportFromOnpService;
use Tests\TestCase;

class UserSettingsTest extends TestCase
{

    /**
     * @test
     */
    public function invalid_get_settings_key_produces_exception() {
        $user = factory(User::class)->make();
        $model = new UserSettings($user);

        $this->expectException(InvalidParameterException::class);
        $model->get('foobar');
    }

    /**
     * @test
     */
    public function default_is_getting_returned() {
        $user = factory(User::class)->make();
        $model = new UserSettings($user);

        $key = array_first(array_keys(UserSettings::SETTINGS));
        $this->assertEquals(UserSettings::SETTINGS[$key]['default'], $model->get($key));
    }

    /**
     * @test
     */
    public function boolean_is_getting_returned() {
        $user = factory(User::class)->make();

        /**
         * true
         */
        $settings = [UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED => 1];
        $user->settings = $settings;

        $model = new UserSettings($user);

        $this->assertSame(true, $model->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED));

        /**
         * false
         */
        $settings = [UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED => 0];
        $user->settings = $settings;

        $model = new UserSettings($user);

        $this->assertSame(false, $model->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED));
    }

    /**
     * @test
     */
    public function invalid_set_settings_key_produces_exception() {
        $user = factory(User::class)->make();
        $model = new UserSettings($user);

        $this->expectException(InvalidParameterException::class);
        $model->get('foobar');
    }

    /**
     * @test
     */
    public function int_is_getting_set_as_boolean() {
        $user = factory(User::class)->make();
        $model = new UserSettings($user);

        $model->set(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED, '1');
        $this->assertSame(true, $user->settings[UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED]);

        $model->set(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED, '0');
        $this->assertSame(false, $user->settings[UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED]);

        $model->set(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED, 1);
        $this->assertSame(true, $user->settings[UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED]);

        $model->set(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED, 0);
        $this->assertSame(false, $user->settings[UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED]);
    }
}