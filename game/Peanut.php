<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game;


use game\interfaces\PeanutInterface;

class Peanut implements PeanutInterface
{

    private $satiety = 1;

    /**
     * Peanut constructor.
     *
     * @param int $satiety
     */
    public function __construct($satiety = 1)
    {
        $this->setSatiety($satiety);
    }

    /**
     * Геттер для приватных свойств объекта у которых есть метод getName
     * @param string $name Имя свойства объекта
     *
     * @return mixed значение свойства объекта
     */
    public function __get($name)
    {
        if (strpos($name, '_')) {
            $name_parts = explode('_', $name);
            $method_name = 'get'.implode('', array_map('ucfirst', $name_parts));
        } else {
            $method_name = 'get'.ucfirst($name);
        }
        return method_exists($this, $method_name) ? $this->$method_name() : null;
    }

    public function __set($name, $value)
    {
        if (strpos($name, '_')) {
            $name_parts = explode('_', $name);
            $method_name = 'set'.implode('', array_map('ucfirst', $name_parts));
        } else {
            $method_name = 'set'.ucfirst($name);
        }

        if (method_exists($this, $method_name)) {
            $this->$method_name($value);
        }
    }

    /**
     * @return int  Сытность ореха
     */
    public function getSatiety()
    {
        return $this->satiety;
    }

    /**
     * Усианавливает значение для свойства $satiety
     *
     * @param int   $satiety    Сытность
     */
    public function setSatiety($satiety)
    {
        if (is_int($satiety) && $satiety > 0) {
            $this->satiety = $satiety;
        } else {
            throw new \InvalidArgumentException('Сытость должна быть целым числом больше 0.');
        }
    }
}