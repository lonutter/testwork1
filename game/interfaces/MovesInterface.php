<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game\interfaces;

use game\Game;


/**
 * Class Moves
 *
 * @package game
 */
interface MovesInterface
{

    /**
     * Singleton
     *
     * @return \game\Moves
     */
    public static function getInstance();

    /**
     * Добавляем сообщение
     *
     * @param   string $message Сообщение
     *
     * @return
     */
    public static function add($message);

    /**
     * Получает список ходов в зависимости от переданных параметров
     *
     * @param null|int $round Порядковый номер (id) раунда
     * @param null|int $move Порядковый номера хода
     *
     * @return array|mixed|null
     */
    public static function get($round = NULL, $move = NULL);

    /**
     * Сохраняет ходы в БД
     */
    public static function save();

    /**
     * @param null $round порядковый номер (id) раунда
     *
     * @return int Количество ходов за всю игру или отдельный раунд
     */
    public static function count($round = NULL);

    /**
     * Новая игра. мы потом возьмем оттуда ID игры
     *
     * @param \game\Game $game
     */
    public static function newGame(Game &$game);

    /**
     * @param $id
     */
    public static function setGame($id);

    /**
     *
     */
    public static function newRound();

    /**
     * @param $id
     */
    public static function setRound($id);

    /**
     * @return string
     */
    public function __toString();
}