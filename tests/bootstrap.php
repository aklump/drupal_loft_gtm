<?php

if (!defined('FILTER_VALIDATE_EMAIL')) {
    define('FILTER_VALIDATE_EMAIL', 274);
}
if (!defined('MENU_NORMAL_ITEM')) {
    define('MENU_NORMAL_ITEM', true);
}
if (!defined('MENU_DEFAULT_LOCAL_TASK')) {
    define('MENU_DEFAULT_LOCAL_TASK', true);
}
if (!defined('MENU_CALLBACK')) {
    define('MENU_CALLBACK', true);
}
require_once dirname(__FILE__) . '/../loft_gtm.module';
require_once dirname(__FILE__) . '/../includes/DataLayer.php';
require_once '/Users/aklump/Code/Packages/drupal/data_api/tests/bootstrap.php';

function t($string, $vars = array())
{
    array_walk($vars, function ($replace, $find) use (&$string) {
        $string = str_replace($find, $replace, $string);
    });

    return $string;
}

function format_plural($count, $singular, $plural, array $args = array(), array $options = array())
{
    if ($count == 1) {
        return $singular;
    }

    return str_replace('@count', $count, $plural);
}

function check_plain($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function valid_email_address($mail)
{
    return (bool) filter_var($mail, FILTER_VALIDATE_EMAIL);
}

function variable_get($var)
{
    global $mock_conf;

    return isset($mock_conf[$var]) ? $mock_conf[$var] : null;
}

function variable_set($var, $value)
{
    global $mock_conf;

    return $mock_conf[$var] = $value;
}

function url($path, $options)
{
    global $mock_url;

    return $mock_url;
}
