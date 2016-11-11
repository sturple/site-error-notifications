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
}
