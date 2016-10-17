<?php
/**
 * Plugin Name: Site Error Notifications
 * Plugin URI: https://github.com/sturple/site-error-notifications
 * Description: Error notifications
 * Version: 0.0.1
 * Author: Shawn Turple / Robert Leahy
 * Author URI: http://turple.ca
 * License: GPU-3.0
 */

call_user_func(function () {
    $rel='vendor/autoload.php';
    $search=[__DIR__.'/',ABSPATH];
    $where=null;
    foreach ($search as $path) {
        $w=$path.$rel;
        if (!file_exists($w)) continue;
        $where=$w;
        break;
    }
    if (is_null($where)) throw new \RuntimeException('Could not find autoloader');
    require_once $where;
});
call_user_func(function () {
    $msg = new \Swift_Message();
    $twig = new \Twig_Environment(
        new \Twig_Loader_Filesystem(__DIR__ . '/templates'),
        [
            'strict_variables' => true
        ]
    );
    $swift = \Swift_Mailer::newInstance(
        \Swift_MailTransport::newInstance()
    );
    //  TODO: Configure message here
    $handler = new \Fgms\SiteErrorNotifications\CompositeErrorHandler();
    $handler->add(
        new \Fgms\SiteErrorNotifications\EmailErrorHandler(
            $msg,
            $swift,
            $twig
        )
    )->add(
        new \Fgms\SiteErrorNotifications\DieErrorHandler()
    );
    set_exception_handler([$handler,'uncaught']);
    set_error_handler([$handler,'error']);
});
