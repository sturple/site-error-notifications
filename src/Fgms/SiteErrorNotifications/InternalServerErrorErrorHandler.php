<?php

namespace Fgms\SiteErrorNotifications;

/**
 * Handles exceptions and errors by sending an HTTP 500
 * to the client.
 */
class InternalServerErrorErrorHandler implements ErrorHandlerInterface
{
    private function send()
    {
        http_response_code(500);
    }

    public function error($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $this->send();
    }

    public function uncaught($ex)
    {
        $this->send();
    }
}
