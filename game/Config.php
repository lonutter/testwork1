<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game;


use game\interfaces\ConfigInterface;

class Config implements ConfigInterface
{
    private static $config = [];

    /**
     * @param array $config
     */
    public static function init(array $config)
    {
        if (empty(self::$config)) {
            self::$config = $config;
        }
    }

    /**
     * @param $name
     * @param null $default_value
     *
     * @return mixed|null
     */
    public static function get($name, $default_value = null)
    {
        return array_key_exists($name, self::$config) ? self::$config[$name] : $default_value;
    }

    /**
     * @param $name
     * @param $value
     */
    public static function set($name, $value)
    {
        if (!array_key_exists($name, self::$config)) {
            self::$config[$name] = $value;
        }
    }

    /**
     * @param $name
     * @param $new_value
     */
    public static function change($name, $new_value)
    {
        if (self::exists($name)) {
            self::$config[$name] = $new_value;
        }
    }

    /**
     * @param $name
     *
     * @return bool
     */
    private static function exists($name)
    {
        return array_key_exists($name, self::$config);
    }
}