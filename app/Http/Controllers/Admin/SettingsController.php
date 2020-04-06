<?php

namespace Mss\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Mss\Http\Controllers\Controller;
use Mss\Http\Requests\AdminSettingsRequest;
use Webklex\IMAP\Client;
use Webklex\IMAP\Exceptions\ConnectionFailedException;

class SettingsController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function show() {
        return view('admin.settings');
    }

    public function save(AdminSettingsRequest $request) {
        $response = $this->setSmtpSettings($request);
        if ($response instanceof RedirectResponse) return $response;

        $response = $this->setImapSettings($request);
        if ($response instanceof RedirectResponse) return $response;

        flash('Einstellungen gespeichert')->success();

        return redirect()->back();
    }

    /**
     * @param AdminSettingsRequest $request
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function setImapSettings(AdminSettingsRequest $request) {
        if (!empty($request->get('imap_host'))) {
            if (!$this->imapSettingsAreValid($request)) {
                flash(__('IMAP Zugangsdaten sind ungültig!'))->error();
                return redirect()->back()->withInput();
            }

            settings()->set([
                'imap.host' => $request->get('imap_host'),
                'imap.port' => $request->get('imap_port'),
                'imap.username' => encrypt($request->get('imap_username')),
                'imap.password' => encrypt($request->get('imap_password')),
                'imap.encryption' => $request->get('imap_encryption'),
            ]);
        } else {
            if (settings()->has('imap.host')) settings()->remove('imap.host');
            if (settings()->has('imap.port')) settings()->remove('imap.port');
            if (settings()->has('imap.username')) settings()->remove('imap.username');
            if (settings()->has('imap.password')) settings()->remove('imap.password');
            if (settings()->has('imap.encryption')) settings()->remove('imap.encryption');
        }

        return null;
    }

    /**
     * @param AdminSettingsRequest $request
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function setSmtpSettings(AdminSettingsRequest $request) {
        if (!empty($request->get('smtp_host'))) {
            if (!$this->smtpSettingsAreValid($request)) {
                flash(__('SMTP Zugangsdaten sind ungültig!'))->error();
                return redirect()->back()->withInput();
            }

            settings()->set([
                'smtp.host' => $request->get('smtp_host'),
                'smtp.port' => $request->get('smtp_port'),
                'smtp.username' => encrypt($request->get('smtp_username')),
                'smtp.password' => encrypt($request->get('smtp_password')),
                'smtp.encryption' => $request->get('smtp_encryption'),
                'smtp.from_address' => $request->get('smtp_from_address'),
                'smtp.from_name' => $request->get('smtp_from_name')
            ]);
        } else {
            if (settings()->has('smtp.host')) settings()->remove('smtp.host');
            if (settings()->has('smtp.port')) settings()->remove('smtp.port');
            if (settings()->has('smtp.username')) settings()->remove('smtp.username');
            if (settings()->has('smtp.password')) settings()->remove('smtp.password');
            if (settings()->has('smtp.encryption')) settings()->remove('smtp.encryption');
            if (settings()->has('smtp.from_address')) settings()->remove('smtp.from_address');
            if (settings()->has('smtp.from_name')) settings()->remove('smtp.from_name');
        }

        return null;
    }

    protected function imapSettingsAreValid(AdminSettingsRequest $request) {
        try {
            $oClient = new Client([
                'host' => $request->get('imap_host'),
                'port' => $request->get('imap_port'),
                'encryption' => $request->get('imap_encryption'),
                'username' => $request->get('imap_username'),
                'password' => $request->get('imap_password'),
                'protocol' => 'imap'
            ]);

            $oClient->connect();

            return true;
        } catch (ConnectionFailedException $e) {
            return false;
        }
    }

    /**
     * @param AdminSettingsRequest $request
     * @return bool
     */
    protected function smtpSettingsAreValid(AdminSettingsRequest $request) {
        try {
            $transport = new \Swift_SmtpTransport($request->get('smtp_host'), $request->get('smtp_port'), $request->get('smtp_encryption'));
            $transport->setUsername($request->get('smtp_username'));
            $transport->setPassword($request->get('smtp_password'));
            $mailer = new \Swift_Mailer($transport);
            $mailer->getTransport()->start();

            return true;
        }
        catch (\Swift_TransportException $e) {
            return false;
        }
    }
}
