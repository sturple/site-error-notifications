<?php

namespace Fgms\SiteErrorNotifications;

/**
 * Decorates an instance of @ref ErrorHandlerInterface
 * invoking it unconditionally on uncaught exception and
 * on error if and only if the error type does not match
 * a bitmask of ignored error types.
 */
class IgnoreErrorHandler extends ConditionalErrorHandler
{
    private $mask;

    /**
     * Creates an IgnoreErrorHandler.
     *
     * @param int $mask
     *  A bitmask of error types to ignore.
     * @param ErrorHandlerInterface $inner
     *  The instance of @ref ErrorHandlerInterface to wrap.
     */
    public function __construct($mask, ErrorHandlerInterface $inner)
    {
        parent::__construct($inner);
        $this->mask = $mask;
    }

    protected function evaluateErrorCondition($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        return ($errno & $this->mask) === 0;
    }
}
