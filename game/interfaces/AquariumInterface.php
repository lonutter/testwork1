<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game\interfaces;

use game\Fishes;
use game\Peanut;

interface AquariumInterface
{

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
     * @return \game\Fishes|null
     */
    public function getFishes();

    /**
     * @return \game\Peanut|false
     */
    public function getPeanut();

    public function getId();

    public function getName();

    public function setName($name);

    /**
     * Погружает рыбок в аквариум.
     *
     * @param \game\Fishes $fishes
     */
    public function putFishes(Fishes $fishes);

    /**
     * Телепортирует орешек в аквариум
     *
     * @param \game\Peanut $peanut
     */
    public function putPeanut(Peanut $peanut);

    /**
     * А есть ли у нас орешек в аквариеме?
     *
     * @return bool
     */
    public function hasPeanut();

    /**
     * Орешек кто-то съел и вызвал этот метод
     *
     * @return bool
     */
    public function peanutEaten();

    /**
     * Этот метод привязывает орешек к рыбе, которая первой до него добралась.
     * Так-то.
     *
     * @param $fish_id
     */
    public function reservePeanut($fish_id);

    /**
     *  Останавливает все движенме в аквариуме.
     */
    public function stopMove();

    /**
     *  Разобрались, можно двигаться дальше.
     */
    public function continueMove();

    /**
     * Может ли рыбка $fish_id скушать орешек, который на данный момент лежит в
     * аквариуме? Давайте спросим у этого метода. Именно для этого он тут.
     *
     * @param $fish_id
     *
     * @return bool
     */
    public function canEat($fish_id);

    /**
     * А это что бы узнать ответ на извечный вопрос.
     * Нет, пока еще не на "какого черта я потратил два дня, что бы написать
     * это?", а всего лишь остановлено ли движение в аквариуме? Рыбки могут
     * плавать?
     *
     * @return bool
     */
    public function stoppedMove();

    /**
     * @param $id
     */
    public function setId($id);

    /**
     * Аналогично Game, только Aquarium
     *
     * @param $id
     *
     * @return \game\Aquarium
     */
    public static function load($id);

    /**
     * Точно так же сохраняет в БД, только не игру, а аквариум. Разумеется
     * образно говоря.
     */
    public function save();
}