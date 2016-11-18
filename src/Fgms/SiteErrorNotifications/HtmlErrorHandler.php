<?php

namespace Fgms\SiteErrorNotifications;

/**
 * Displays an HTML page rendered using Twig templates
 * when an error or uncaught exception occurs.
 */
class HtmlErrorHandler implements ErrorHandlerInterface
{
    private $twig;
    private $error;
    private $exception;

    /**
     * Creates a new HtmlErrorHandler.
     *
     * @param Twig_Environment $twig
     * @param string $error
     *  The template to render on error.
     * @param string $exception
     *  The template to render on exception.
     */
    public function __construct(\Twig_Environment $twig, $error, $exception)
    {
        $this->twig = $twig;
        $this->error = $error;
        $this->exception = $exception;
    }

    public function error($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        echo(Utility::renderError($errno,$errstr,$errfile,$errline,$errcontext,$this->error,$this->twig));
    }

    public function uncaught($ex)
    {
        echo(Utility::renderException($ex,$this->exception,$this->twig));
    }
}
