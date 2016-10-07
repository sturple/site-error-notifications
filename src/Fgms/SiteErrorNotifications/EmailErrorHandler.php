<?php

namespace Fgms\SiteErrorNotifications;

class EmailErrorHandler implements ErrorHandlerInterface
{
    private $message;
    private $swift;

    public function __construct(\Swift_Message $message, \Swift_Mailer $swift)
    {
        $this->message = $message;
        $this->swift = $swift;
    }

    public function error($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $msg = clone $this->message;
        $msg->setSubject('Error')
            ->setBody('Error');
        $this->swift->send($msg);
        return false;
    }

    public function uncaught($ex)
    {
        $msg = clone $this->message;
        $msg->setSubject('Exception')
            ->setBody('Exception');
        $this->swift->send($msg);
    }
}
