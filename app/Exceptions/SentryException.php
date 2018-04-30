<?php

namespace Mss\Exceptions;

use Exception;

class SentryException extends Exception {
    /**
     * @var array
     */
    public $context;

    /**
     * SentryException constructor.
     * @param string $message
     * @param array $context
     */
    public function __construct($message = "", $context = []) {
        $this->context = $context;
        app('sentry')->user_context($context);
        parent::__construct($message, 0, null);
    }
}