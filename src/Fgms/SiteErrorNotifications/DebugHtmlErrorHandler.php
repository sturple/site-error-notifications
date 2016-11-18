<?php

namespace Fgms\SiteErrorNotifications;

/**
 * Displays an HTML page with detailed error information
 * when an error or uncaught exception occurs.
 *
 * Due to the fact this may display sensitive system
 * information this handler should not be used in production
 * environments.
 */
class DebugHtmlErrorHandler extends HtmlErrorHandler
{
    /**
     * Creates a DebugHtmlErrorHandler.
     *
     * @param Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        parent::__construct($twig,'errordebug.html.twig','exceptiondebug.html.twig');
    }
}
