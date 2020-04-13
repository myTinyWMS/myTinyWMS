<?php

namespace Mss\Services;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Mss\Models\OrderMessage;

class ConfigService {

    public static function setConfigFromSettings() {
        settings()->flushCache();

        config([
            'mail.host' => settings('smtp.host'),
            'mail.port' => settings('smtp.port'),
            'mail.encryption' => settings('smtp.encryption'),
            'mail.username' => !empty(settings('smtp.username')) ? decrypt(settings('smtp.username')) : null,
            'mail.password' => !empty(settings('smtp.password')) ? decrypt(settings('smtp.password')) : null,
            'mail.from.address' => settings('smtp.from_address'),
            'mail.from.name' => settings('smtp.from_name')
        ]);

        config([
            'imap.accounts.default.host' => settings('imap.host'),
            'imap.accounts.default.port' => settings('imap.port'),
            'imap.accounts.default.encryption' => settings('imap.encryption'),
            'imap.accounts.default.username' => !empty(settings('imap.username')) ? decrypt(settings('imap.username')) : null,
            'imap.accounts.default.password' => !empty(settings('imap.password')) ? decrypt(settings('imap.password')) : null,
        ]);
    }

}