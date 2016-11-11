<?php

namespace Fgms\SiteErrorNotifications;

class Utility
{
    public static function getBacktrace()
    {
        $bt = debug_backtrace();
        $bt = array_reverse($bt);
        $retr = [];
        foreach ($bt as $frame) {
            if (!(isset($frame['file']) && isset($frame['line']))) break;
            $retr[] = $frame;
        }
        return array_reverse($retr);
    }

    public static function getErrorLevelName($errno)
    {
        $constants = get_defined_constants(true);
        $core = $constants['Core'];
        $levels = [];
        foreach ($core as $name => $value) {
            if (preg_match('/^E\\_/u',$name)) $levels[$value] = $name;
        }
        if (isset($levels[$errno])) return $levels[$errno];
        throw new \LogicException(
            sprintf(
                '%d does not correspond to any PHP error level',
                $errno
            )
        );
    }
}
