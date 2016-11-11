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
            if (isset($frame['class']) && is_subclass_of('\\' . $frame['class'],'\\Fgms\\SiteErrorNotifications\\ErrorHandlerInterface')) break;
            $retr[] = $frame;
        }
        return array_reverse($retr);
    }
}
