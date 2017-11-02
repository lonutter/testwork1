<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game\interfaces;

interface ConfigInterface
{

    /**
     * @param array $config
     */
    public static function init(array $config);

    /**
     * @param $name
     * @param null $default_value
     *
     * @return mixed|null
     */
    public static function get($name, $default_value = NULL);

    /**
     * @param $name
     * @param $value
     */
    public static function set($name, $value);

    /**
     * @param $name
     * @param $new_value
     */
    public static function change($name, $new_value);
}