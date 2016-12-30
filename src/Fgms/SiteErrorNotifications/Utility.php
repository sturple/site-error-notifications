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

    private static function isErrorLevelName($name)
    {
        return !!preg_match('/^E\\_/u',$name);
    }

    public static function getErrorLevelName($errno)
    {
        $constants = get_defined_constants(true);
        $core = $constants['Core'];
        $levels = [];
        foreach ($core as $name => $value) {
            if (self::isErrorLevelName($name)) $levels[$value] = $name;
        }
        if (isset($levels[$errno])) return $levels[$errno];
        throw new \LogicException(
            sprintf(
                '%d does not correspond to any PHP error level',
                $errno
            )
        );
    }

    private static function raiseNotAnErrorLevel($name)
    {
        throw new \LogicException(
            sprintf(
                '"%s" is not a PHP error level',
                $name
            )
        );
    }

    public static function getErrorLevelNum($name)
    {
        if (!self::isErrorLevelName($name)) self::raiseNotAnErrorLevel($name);
        $constants = get_defined_constants(true);
        $core = $constants['Core'];
        if (!isset($core[$name])) self::raiseNotAnErrorLevel($name);
        return $core[$name];
    }

    public static function renderError($errno, $errstr, $errfile, $errline, array $errcontext, $template_name, \Twig_Environment $twig, array $ctx = [])
    {
        $template = $twig->loadTemplate($template_name);
        return $template->render(array_merge([
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline,
            'errcontext' => $errcontext,
            'backtrace' => self::getBacktrace(),
            'errlevel' => self::getErrorLevelName($errno)
        ],$ctx));
    }

    public static function renderException($ex, $template_name, \Twig_Environment $twig, array $ctx = [])
    {
        //  What if this happens multiple times?
        $twig->addFunction(
            new \Twig_SimpleFunction(
                'class',
                function ($obj) {   return get_class($obj); }
            )
        );
        $template = $twig->loadTemplate($template_name);
        return $template->render(array_merge(['ex' => $ex],$ctx));
    }
}
