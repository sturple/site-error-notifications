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

require_once(ABSPATH . 'wp-admin/includes/plugin.php');
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
    //  Replace this with the path to your error
    //  handler configuration file
    $config = __DIR__ . '/../config.yml';
    $yaml = file_get_contents($config);
    if ($yaml === false) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            sprintf(
                'Could not open configuration file %s',
                $config
            )
        );
    }
    try {
        $handler = \Fgms\SiteErrorNotifications\YamlFactory::create($yaml);
    } catch (\Fgms\SiteErrorNotifications\InvalidYamlException $e) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            sprintf(
                'Error parsing configuration file %s: %s',
                $config,
                $e->getMessage()
            )
        );
    }
    set_exception_handler([$handler,'uncaught']);
    set_error_handler(function () use ($handler) {
        call_user_func_array([$handler,'error'],func_get_args());
        return true;
    },E_ALL|E_STRICT);
});
