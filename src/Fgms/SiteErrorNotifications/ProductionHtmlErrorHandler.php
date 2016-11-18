<?php

namespace Fgms\SiteErrorNotifications;

/**
 * Displays an HTML page with a simple error
 * message which does not reveal detailed information
 * when a PHP error or uncaught exception occurs.
 *
 * Due to the fact this does not display any details
 * about the precise nature of the error which occurred
 * this handler is suitable for use in production
 * environments.
 */
class ProductionHtmlErrorHandler extends HtmlErrorHandler
{
    /**
     * Creates a ProductionHtmlErrorHandler.
     *
     * @param Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        parent::__construct($twig,'500.html.twig','500.html.twig');
    }
}
