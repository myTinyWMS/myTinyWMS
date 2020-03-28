<?php

namespace Mss\Exceptions;

use Exception;
use Sentry\State\Hub;
use Sentry\State\Scope;

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

        Hub::getCurrent()->configureScope(function (Scope $scope) use ($context) {
            $scope->setUser($context);
        });

        parent::__construct($message, 0, null);
    }
}