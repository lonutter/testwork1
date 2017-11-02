<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game;


use game\interfaces\AquariumInterface;
use PDO;

class Aquarium implements AquariumInterface
{
    private
        $id,
        $name,
        $reserve_peanut = 0,
        $is_reserved = false,
        $canMove = true,
        /**
         * @var \game\Peanut
         */
        $peanut = FALSE,
        /**
         * @var \game\Fishes
         */
        $fishes = null;

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
     * @return \game\Fishes|null
     */
    public function getFishes()
    {
        return $this->fishes;
    }

    /**
     * @return \game\Peanut|false
     */
    public function getPeanut()
    {
        return $this->peanut;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Погружает рыбок в аквариум.
     *
     * @param \game\Fishes $fishes
     */
    public function putFishes(Fishes $fishes)
    {
        $this->fishes = $fishes;
    }

    /**
     * Телепортирует орешек в аквариум
     *
     * @param \game\Peanut $peanut
     */
    public function putPeanut(Peanut $peanut)
    {
        $this->peanut = $peanut;
        Moves::add("Новый орешек телепортировали в аквариум.");
    }

    /**
     * А есть ли у нас орешек в аквариеме?
     * @return bool
     */
    public function hasPeanut()
    {
        return (bool) $this->peanut;
    }

    /**
     * Орешек кто-то съел и вызвал этот метод
     *
     * @return bool
     */
    public function peanutEaten()
    {
        $this->peanut = false;
        return true;
    }

    /**
     * Этот метод привязывает орешек к рыбе, которая первой до него добралась. Так-то.
     *
     * @param $fish_id
     */
    public function reservePeanut($fish_id)
    {
        //Все гениальное - просто xD
        //Но это не тот случай... В том смысле, что это не гениально.
        $this->reserve_peanut = $fish_id;
    }

    /**
     *  Останавливает все движенме в аквариуме.
     */
    public function stopMove()
    {
        $this->canMove = false;
    }

    /**
     *  Разобрались, можно двигаться дальше.
     */
    public function continueMove()
    {
        $this->canMove = true;
    }

    /**
     * Может ли рыбка $fish_id скушать орешек, который на данный момент лежит в аквариуме?
     * Давайте спросим у этого метода. Именно для этого он тут.
     *
     * @param $fish_id
     *
     * @return bool
     */
    public function canEat($fish_id)
    {
        return !$this->is_reserved || ($this->is_reserved && $this->reserve_peanut == $fish_id);
    }

    /**
     * А это что бы узнать ответ на извечный вопрос.
     * Нет, пока еще не на "какого черта я потратил два дня, что бы написать это?",
     * а всего лишь остановлено ли движение в аквариуме? Рыбки могут плавать?
     * @return bool
     */
    public function stoppedMove()
    {
        return !$this->canMove;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Аналогично Game, только Aquarium
     *
     * @param $id
     *
     * @return \game\Aquarium
     */
    public static function load($id)
    {
        if (is_int($id) && $id > 0) {
            $stmt = Db::getConnection()->prepare('select * from `aquariums` where `id`=:id');
            $stmt->bindValue('id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch();

            $aquarium = new self();
            $aquarium->setId($data['id']);
            $aquarium->setName($data['name']);

            $aquarium->fishes = Fishes::findInAquarium($aquarium);

            return $aquarium;
        } else {
            throw new \InvalidArgumentException('Параметр $id должен быть целым числом больше 0.');
        }
    }

    /**
     * Точно так же сохраняет в БД, только не игру, а аквариум. Разумеется образно говоря.
     */
    public function save()
    {
        $connection = Db::getConnection();

        if (is_null($this->id)) {
            $stmt = $connection->prepare('insert into `aquariums` set `name`=:name');
        } else {
            $stmt = $connection->prepare('update `aquariums` set `name`=:name where `id`=:id');
            $stmt->bindValue('id', $this->id, PDO::PARAM_INT);
        }

        $stmt->bindValue('name', $this->name);
        $stmt->execute();
        //Не уверен, что это нормальная конструкция, но сейчас 5:51, так что сойдет.
        if (is_null($this->id)) {
            $this->id = $connection->lastInsertId();
        }
        $this->fishes->save();  //И про рыбок не забываем.
    }
}