<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game\interfaces;

interface DbInterface
{

    /**
     * @return \PDO
     */
    public static function getConnection();
}