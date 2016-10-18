<?php

namespace Fgms\SiteErrorNotifications;

/**
 * Logs errors to a PSR-3 log.
 */
class Psr3ErrorHandler implements ErrorHandlerInterface
{
    private $log;
    private $newline = "\r\n";

    /**
     * Creates a new Psr3ErrorHandler.
     *
     * @param LoggerInterface $log
     *  A PSR-3 logger which shall be used to log
     *  any errors which are reported.
     */
    public function __construct(\Psr\Log\LoggerInterface $log)
    {
        $this->log = $log;
    }

    private function formatBacktrace(array $bt)
    {
        $retr = '';
        $i = 1;
        foreach ($bt as $frame) {
            if ($i !== 1) $retr .= $this->newline;
            $retr .= sprintf(
                "%d.\t",
                $i++
            );
            if (isset($frame['type'])) $retr .= sprintf(
                '%s%s%s',
                $frame['class'],
                $frame['type'],
                $frame['function']
            );
            else $retr .= $frame['function'];
            $retr .= sprintf(
                ' at %s:%d',
                $frame['file'],
                $frame['line']
            );
        }
        return $retr;
    }

    public function error($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $msg = sprintf(
            'PHP Error %s (%d) at %s:%d:',
            $errstr,
            $errno,
            $errfile,
            $errline
        );
        $msg .= $this->newline;
        $msg .= $this->formatBacktrace(Utility::getBacktrace());
        $this->log->error($msg);
        return false;
    }

    public function uncaught($ex)
    {
        $i = 1;
        $curr = $ex;
        $msg = 'Uncaught Exception';
        do {
            $msg .= $this->newline;
            $msg .= sprintf(
                '#%d: %s: %s (%d) at %s:%d:',
                $i,
                get_class($curr),
                $curr->getMessage(),
                $curr->getCode(),
                $curr->getFile(),
                $curr->getLine()
            );
            $msg .= $this->newline;
            $msg .= $this->formatBacktrace($curr->getTrace());
            ++$i;
        } while (!is_null($curr = $curr->getPrevious()));
        $this->log->error($msg);
    }
}
