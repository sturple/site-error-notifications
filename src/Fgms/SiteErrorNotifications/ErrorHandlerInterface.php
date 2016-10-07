<?php

namespace Fgms\SiteErrorNotifications;

/**
 * An interface which may be implemented to capture
 * errors.
 */
interface ErrorHandlerInterface
{
    public function error($errno, $errstr, $errfile, $errline, array $errcontext);
    public function uncaught($ex);
}
