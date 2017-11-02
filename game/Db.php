<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game;


use game\interfaces\DbInterface;
use PDO;

class Db implements DbInterface
{
    private static $connection;

    /**
     * @return \PDO
     */
    public static function getConnection()
    {
        if (!self::$connection) {
            $data = Config::get('db');
            self::$connection = new PDO($data['dsn'], $data['user'], $data['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$connection;
    }
}