<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game\interfaces;

use game\Game;


/**
 * Class Round
 *
 * @package game
 */
interface RoundInterface
{

    /**
     * Round constructor.
     *
     * @param \game\Game $game
     */
    public function __construct(Game $game);

    /**
     * Геттер для приватных свойств объекта у которых есть метод getName
     *
     * @param string $name Имя свойства объекта
     *
     * @return mixed значение свойства объекта
     */
    public function __get($name);

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * Возвращает список ходов в этом раунде.
     *
     * @return array|mixed|null
     */
    public function getMoves();

    /**
     *  Выполняет ход
     */
    public function play();

    /**
     * @param $id
     */
    public function setId($id);
}