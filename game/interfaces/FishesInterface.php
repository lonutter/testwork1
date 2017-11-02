<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game\interfaces;

use game\Aquarium;


/**
 * Class Fishes
 *  Рыбки плавают группками.
 *
 * @package game
 */
interface FishesInterface
{

    /**
     * Fishes constructor. <- неожиданно, правда?
     *
     * @param array $fishes
     * @param \game\Aquarium $aquarium
     */
    public function __construct(array $fishes, Aquarium $aquarium);

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
     * Этот метод я использую когда хочу проверить на обожрались ли там еще
     * рыбки
     *
     * @return int
     */
    public function getSatietySum();

    /** А сколько всего рыбок в нашем аквариуме?
     *
     * @return int
     */
    public function getCount();

    /** Разновидности рыбок
     * Да, не таккая у нашего аквариума и богатая "фауна"
     *
     * @return array
     */
    public function getTypes();

    /**
     * Связываем разновидности рыб с классами из особенностей
     *
     * @return array
     */
    public function getTypesFeatures();

    /**
     * Сделать ход всем рыбам
     *
     * @return bool
     *
     */
    public function go();

    /**
     * Эфпять. Все аналогично Game и Aquarium, только для Fishes.
     */
    public function save();

    /**
     * Найдем всех жителей нашего аквариума.
     *
     * @param \game\Aquarium $aquarium
     *
     * @return \game\Fishes
     */
    public static function findInAquarium(Aquarium $aquarium);
}