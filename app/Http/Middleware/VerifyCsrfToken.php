<?php

namespace Mss\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/order/*/message/upload',
        '/order/*/invoicecheck/upload',
        '/article/*/file_upload',
        '/article/fix-inventory'
    ];
}
