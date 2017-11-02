<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game\interfaces;

interface PeanutInterface
{

    /**
     * Peanut constructor.
     *
     * @param int $satiety
     */
    public function __construct($satiety = 1);

    /**
     * Геттер для приватных свойств объекта у которых есть метод getName
     *
     * @param string $name Имя свойства объекта
     *
     * @return mixed значение свойства объекта
     */
    public function __get($name);

    public function __set($name, $value);

    /**
     * @return int  Сытность ореха
     */
    public function getSatiety();

    /**
     * Усианавливает значение для свойства $satiety
     *
     * @param int $satiety Сытность
     */
    public function setSatiety($satiety);
}