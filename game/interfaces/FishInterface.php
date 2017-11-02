<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game\interfaces;

use game\Aquarium;


/**
 * Class Fish
 *
 * @package game
 */
interface FishInterface
{

    /**
     * Fish constructor.
     *
     * @param $type
     * @param $name
     * @param $speed
     * @param $satiety
     * @param \game\Aquarium $aquarium
     * @param array $events
     */
    public function __construct($type, $name, $speed, $satiety, Aquarium $aquarium, array $events = []);

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
     * Сколько там еще осталось раундов до разблокировки? Ответ скажет этот
     * метод.
     *
     * @return int
     */
    public function getBlockedMoves();

    /**
     * Согласен, странное название. Как и многое другое в этом задании :-)
     *
     * @return int
     */
    public function getBlockedEats();

    /**
     * @return \game\Aquarium|null
     */
    public function getAquarium();

    /**
     * @return null
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string Разновидность (тип) рыбы
     */
    public function getType();

    /**
     * @return int Скорость рыбы
     */
    public function getSpeed();

    /**
     * @return int  Сытость рыбы
     */
    public function getSatiety();

    /**
     *  Высчитывает скорость во врямя хода
     */
    public function calculateSpeed();

    /**
     * Уменьшает на единицу количество оставшихся раундов блока
     */
    public function decrementBlockedEats();

    /**
     * Увеличивает. Им мы не пользуемся, просто что бы вы знали что еще и так
     * можно :-)
     */
    public function incrementBlockedEats();

    /**
     * Аналогично *Eats
     */
    public function decrementBlockedMoves();

    /**
     * Аналогично *Eats
     */
    public function incrementBlockedMoves();

    /**
     * @param $id
     */
    public function setId($id);

    /**
     * Устанавливаем скорость рыбы
     *
     * @param $speed int Скорость рыбы
     */
    public function setSpeed($speed);

    /**
     * Устанавливает сытость рыбы
     *
     * @param $satiety  int Сытость рыбы
     */
    public function setSatiety($satiety);

    /**
     * @param $name
     */
    public function setName($name);

    /**
     * @param $type
     */
    public function setType($type);

    /**
     * Добавляет список обработчиков событий
     *
     * @param array $events Массив обработчиков
     */
    public function addEvents(array $events);

    /**
     * Добавляет обработчик события
     *
     * @param string $type Тип события
     * @param callable $callback Обработчик события
     * @param array $args Список аргументов для обработчика события $callback
     */
    public function addEvent($type, callable $callback, array $args = []);

    /**
     * Хватит жрать.
     *
     * @param int $num_rounds
     */
    public function blockEat($num_rounds = 1);

    /**
     * Ну если только чуть-чуть и не на ночь.
     */
    public function unblockEat();

    /**
     * Проверяет может ли рыба съесть орешек
     *
     * @return bool Может ли рыба съесть орешек
     */
    public function canEat();

    /**
     * Ест орех.
     * Добавляет к своей сытости сытость ореха.
     *
     * @return bool
     */
    public function eat();

    /**
     * Ох уж эта странное стремление все заблокировать
     *
     * @param int $num_moves
     */
    public function blockMove($num_moves = 1);

    /**
     * Заблокировать наоборот
     */
    public function unblockMove();

    /**
     * кен
     *
     * @return bool
     */
    public function canMove();

    /**
     * Ура! Наконец-то походим! Да, эти рыбы ходят.
     *
     * @return null
     */
    public function move();

    /**
     * Сохранимся
     */
    public function save();
}