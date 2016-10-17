<?php

namespace Fgms\SiteErrorNotifications;

class EmailErrorHandler implements ErrorHandlerInterface
{
    private $message;
    private $swift;
    private $twig;
    private $name;

    public function __construct(\Swift_Message $message, \Swift_Mailer $swift, \Twig_Environment $twig, $name = null)
    {
        $this->message = $message;
        $this->swift = $swift;
        $this->twig = $twig;
        $this->name = $name;
        //  TODO: Try and localize this so that
        //  we don't pollute the entire Twig environment
        $this->twig->addFunction(
            new \Twig_SimpleFunction(
                'class',
                function ($obj) {   return get_class($obj); }
            )
        );
    }

    private function getSubject($str)
    {
        if (is_null($this->name)) return $str;
        return sprintf(
            '%s: %s',
            $this->name,
            $str
        );
    }

    private function getMessage()
    {
        $retr = clone $this->message;
        $retr->setContentType('text/html');
        return $retr;
    }

    public function error($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $subject = sprintf(
            '%s at %s:%d',
            $errstr,
            $errfile,
            $errline
        );
        $subject = $this->getSubject($subject);
        $template = $this->twig->loadTemplate('erroremail.html.twig');
        $msg = $this->getMessage();
        $msg->setSubject($subject);
        $ctx = [
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline,
            'errcontext' => $errcontext,
            'backtrace' => Utility::getBacktrace()
        ];
        $msg->setBody($template->render($ctx));
        $this->swift->send($msg);
        return false;
    }

    public function uncaught($ex)
    {
        $subject = sprintf(
            '%s at %s:%d',
            get_class($ex),
            $ex->getFile(),
            $ex->getLine()
        );
        $subject = $this->getSubject($subject);
        $template = $this->twig->loadTemplate('exceptionemail.html.twig');
        $msg = $this->getMessage();
        $msg->setSubject($subject);
        $ctx = [
            'ex' => $ex
        ];
        $msg->setBody($template->render($ctx));
        $this->swift->send($msg);
    }
}
