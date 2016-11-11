<?php

namespace Fgms\SiteErrorNotifications;

/**
 * Allows multiple error handlers to be bundled together
 * and treated as one.
 *
 * When an error occurs each bundled error handler shall
 * be invoked in the order in which they were added to the
 * composite.
 */
class CompositeErrorHandler implements ErrorHandlerInterface
{
    private $children;

    /**
     * Creates a new CompositeErrorHandler.
     */
    public function __construct()
    {
        $this->children = [];
    }

    /**
     * Adds an error handler to the composite.
     *
     * @param ErrorHandlerInterface $handler
     *
     * @return CompositeErrorHandler
     */
    public function add(ErrorHandlerInterface $handler)
    {
        $this->children[] = $handler;
        return $this;
    }

    public function error($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        foreach ($this->children as $child) $child->error($errno,$errstr,$errfile,$errline,$errcontext);
    }

    public function uncaught($ex)
    {
        foreach ($this->children as $child) $child->uncaught($ex);
    }
}
