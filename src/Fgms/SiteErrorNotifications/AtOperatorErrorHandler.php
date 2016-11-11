<?php

namespace Fgms\SiteErrorNotifications;

/**
 * Decorates an instance of @ref ErrorHandlerInterface
 * invoking it unconditionally on uncaught exception and
 * on on error only when the statement which caused the error
 * was not prepended by the \@ error control operator. 
 */
class AtOperatorErrorHandler extends ConditionalErrorHandler
{
    protected function evaluateErrorCondition($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        //  From http://php.net/manual/en/language.operators.errorcontrol.php:
        //
        //  If you have set a custom error handler function with set_error_handler()
        //  then it will still get called, but this custom error handler can (and
        //  should) call error_reporting() which will return 0 when the call that
        //  triggered the error was preceded by an @.
        //
        //  Accordingly we only want to pass through to the nested ErrorHandlerInterface
        //  when error_reporting does NOT return zero.
        return error_reporting() !== 0;
    }
}
