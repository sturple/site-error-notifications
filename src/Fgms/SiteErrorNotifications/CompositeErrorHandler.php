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
    private $retr;

    /**
     * Creates a new CompositeErrorHandler.
     *
     * @param bool $retr
     *  The value which shall be returned by the error
     *  handler.  Defaults to \em false which allows the
     *  normal error handler to continue.
     */
    public function __construct($retr = false)
    {
        $this->children = [];
        $this->retr = false;
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
        return $this->retr;
    }

    public function uncaught($ex)
    {
        foreach ($this->children as $child) $child->uncaught($ex);
    }
}
