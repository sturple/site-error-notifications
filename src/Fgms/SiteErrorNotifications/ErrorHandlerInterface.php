<?php

namespace Fgms\SiteErrorNotifications;

/**
 * An interface which may be implemented to capture
 * errors.
 */
interface ErrorHandlerInterface
{
    /**
     * Invoked on a PHP error.
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     *
     * @return bool
     */
    public function error($errno, $errstr, $errfile, $errline, array $errcontext);

    /**
     * Invoked on an uncaught exception.
     *
     * @param $ex
     *  The uncaught exception.  In PHP 5 this must be
     *  an Exception object.  In PHP 7 this must be a
     *  Throwable object.
     */
    public function uncaught($ex);
}
