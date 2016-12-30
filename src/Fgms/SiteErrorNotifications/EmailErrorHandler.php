<?php

namespace Fgms\SiteErrorNotifications;

/**
 * Sends notifications emails when an error
 * occurs.
 */
class EmailErrorHandler implements ErrorHandlerInterface
{
    private $message;
    private $swift;
    private $twig;
    private $name;

    /**
     * Creates a new EmailErrorHandler.
     *
     * @param Swift_Message $message
     *  A Swift_Message instance which shall serve as
     *  a prototype for messages sent by the newly
     *  created instance.  This object shall be cloned
     *  and then the subject, body, and content type
     *  shall be set appropriately, all other options
     *  shall remain unchanged.
     * @param Swift_Mailer $mailer
     *  Will be used to send emails.
     * @param Twig_Environment $twig
     *  Will be used to render templates to obtain email
     *  bodies.
     * @param string|null $name
     *  A name for this site that shall be used to identify
     *  emails sent by this instance.  May be null in which
     *  case this site is unnamed.
     */
    public function __construct(\Swift_Message $message, \Swift_Mailer $swift, \Twig_Environment $twig, $name = null)
    {
        $this->message = $message;
        $this->swift = $swift;
        $this->twig = $twig;
        $this->name = $name;
    }

    private function getSubject($str)
    {
        $retr = sprintf('Fg Error %s',$str);
        if (is_null($this->name)) return $retr;
        return sprintf('%s | %s',$retr,$this->name);
    }

    private function getMessage()
    {
        $retr = clone $this->message;
        $retr->setContentType('text/html');
        return $retr;
    }

    public function error($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $subject = $this->getSubject('PHP Error');
        $msg = $this->getMessage();
        $msg->setSubject($subject);
        $msg->setBody(Utility::renderError($errno,$errstr,$errfile,$errline,$errcontext,'erroremail.html.twig',$this->twig));
        $this->swift->send($msg);
    }

    public function uncaught($ex)
    {
        $subject = $this->getSubject('Uncaught Exception');
        $msg = $this->getMessage();
        $msg->setSubject($subject);
        $msg->setBody(Utility::renderException($ex,'exceptionemail.html.twig',$this->twig));
        $this->swift->send($msg);
    }
}
