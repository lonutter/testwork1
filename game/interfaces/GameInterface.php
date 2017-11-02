<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game\interfaces;

use game\Aquarium;


/**
 * Class Game
 *  Управляет ходом игры через класс Round
 *
 * @package game
 */
interface GameInterface
{

    /**
     * Game constructor.
     *
     * @param \game\Aquarium $aq
     * @param int $num_peanuts
     */
    public function __construct(Aquarium $aq, $num_peanuts = 2);

    /**
     * Геттер для приватных свойств объекта
     *
     * @param string $name Имя свойства объекта
     *
     * @return mixed значение свойства объекта
     */
    public function __get($name);

    /**
     * Сеттер приватных свойств
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value);

    /**
     * @return null|int Идентификатор игры
     */
    public function getId();

    /**
     * @return \game\Aquarium
     */
    public function getAquarium();

    /**
     * @return int Количество орехов в игре
     */
    public function getNumPeanuts();

    /**
     * @param $id
     */
    public function setId($id);

    /**
     * @param $num_peanuts
     */
    public function setNumPeanuts($num_peanuts);

    /**
     * Загружает игру из БД по ее ID
     *
     * @param $id
     *
     * @return \game\Game
     */
    public static function load($id);

    /**
     * Сохраняет игру в БД
     *
     * @return bool
     */
    public function save();

    /**
     * Удаляет игру из БД
     *
     * @return bool|null
     */
    public function delete();

    /**
     * @return int
     */
    public function getRoundId();

    /**
     * @param int $id
     *
     * @return mixed|null
     */
    public function getRound($id = 1);

    /**
     * Текущий орех съеден. Уменьшаем общее количество орехов/
     */
    public function peanutEaten();
}