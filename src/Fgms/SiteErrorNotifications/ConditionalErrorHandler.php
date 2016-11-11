<?php

namespace Fgms\SiteErrorNotifications;

/**
 * Implements the template method pattern to simplify
 * the implementation of classes which implement
 * @ref ErrorHandlerInterface and which conditionally
 * pass errors or exceptions through to a nested
 * @ref ErrorHandlerInterface.
 */
abstract class ConditionalErrorHandler extends ErrorHandlerDecorator
{
    public function error($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        return $this->evaluateErrorCondition($errno,$errstr,$errfile,$errline,$errcontext) && parent::error($errno,$errstr,$errfile,$errline,$errcontext);
    }

    /**
     * Evaluates the conditions under which the nested
     * @ref ErrorHandlerInterface should be invoked on
     * error.
     *
     * Provided implementation unconditionally returns
     * \em true.
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     *
     * @return bool
     *  \em true if the inner @ref ErrorHandlerInterface
     *  should be invoked, \em false otherwise.
     */
    protected function evaluateErrorCondition($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        return true;
    }

    public function uncaught($ex)
    {
        return $this->evaluateUncaughtCondition($ex) && parent::uncaught($ex);
    }

    /**
     * Evaluates the conditions under which the nested
     * @ref ErrorHandlerInterface should be invoked on
     * uncaught exception.
     *
     * Provided implementation unconditionally returns
     * \em true.
     *
     * @param $ex
     *  The uncaught exception.  In PHP 5 this must be
     *  an Exception object.  In PHP 7 this must be a
     *  Throwable object.
     */
    protected function evaluateUncaughtCondition($ex)
    {
        return true;
    }
}
