<?php

namespace Fgms\SiteErrorNotifications;

/**
 * A base class for classes implementing
 * @ref ErrorHandlerInterface and the decorator
 * pattern.
 */
abstract class ErrorHandlerDecorator implements ErrorHandlerInterface
{
    private $inner;

    /**
     * Creates a new ErrorHandlerDecorator.
     *
     * @param ErrorHandlerInterface $inner
     *  The @ref ErrorHandlerInterface instance to
     *  decorate.
     */
    public function __construct(ErrorHandlerInterface $inner)
    {
        $this->inner = $inner;
    }

    public function error($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        return $this->inner->error($errno,$errstr,$errfile,$errline,$errcontext);
    }

    public function uncaught($ex)
    {
        $this->inner->uncaught($ex);
    }
}
