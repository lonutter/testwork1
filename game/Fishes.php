<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game;

use game\interfaces\FishesInterface;

/**
 * Class Fishes
 *  Рыбки плавают группками.
 * @package game
 */
class Fishes implements FishesInterface
{
    const FISH_TYPE_CARP = 1;
    const FISH_TYPE_STURGEON = 2;
    const FISH_TYPE_PIKE = 3;

    private
        /**
         * @var \game\Aquarium
         */
        $aquarium = null,
        $fishes = [],
        $fish_fields = ['name', 'type', 'speed', 'satiety'];

    /**
     * Fishes constructor. <- неожиданно, правда?
     *
     * @param array $fishes
     * @param \game\Aquarium $aquarium
     */
    public function __construct(array $fishes, Aquarium $aquarium)
    {
        //Все просто: принимаем массив с данными по рыбкам, а после передаем его в один метод, а тот метод в другой метод
        //И так до тех пор пока рабки не заведуться
        //В аквариуме
        $this->aquarium = $aquarium;
        $this->createFishes($fishes);
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

    /**
     * @param $name
     * @param $value
     */
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
     * Вот этот метод создает новый экземпляр класса Fish. Наконец-то.
     *
     * @param $type
     * @param $name
     * @param $speed
     * @param $satiety
     * @param null $id
     */
    private function createFish($type, $name, $speed, $satiety, $id = null)
    {
        //Что бы отсеить левые разновидности рыб. Нам в наших аквариумах кого попало не нужно.
        if (!array_key_exists($type, $this->getTypes())) {
            //Действем решительно
            throw new \InvalidArgumentException("Разновидность рыбы ($type) указана не верно.");
        }
        //А вот это что-то вроде событий. Хуки. В общем что-то из этого ряда.
        $events = [];
        $types_features = $this->getTypesFeatures();

        //Ищем и находим классы для особенностей наших рыбок
        if (array_key_exists($type, $types_features)) {
            $type_features = new $types_features[$type]();
            $events = $type_features->getEvents();  //Назначаем обработчики событий
        }

        //Ура! Новый житель аквариума!
        $fish = new Fish($type, $name, $speed, $satiety, $this->aquarium, $events);

        if (!is_null($id)) {
            $fish->setId($id);
        }
        //С новосельем!
        $this->fishes[] = $fish;
    }

    /**
     * Вот тут мы подготавливаем масси с даннми на рыбок для передачи методу выше.
     *
     * @param array $fishes
     */
    private function createFishes(array $fishes)
    {
        foreach ($fishes as $fish) {
            //ААА!! СЛОЖНААА!! Так вы подумали?
            //На самом деле все просто: получаем названия полей для класса Fish, которых нет в переданных данных на рыб
            if (empty(($diff = array_diff($this->fish_fields, array_keys($fish))))) {
                $id = array_key_exists('id', $fish) ? $fish['id'] : null;   //На случай... Нет, не важных переговоров, а отсутствия ID (так бывает, если рыбка новая, а не из таблицы БД
                $this->createFish($fish['type'], $fish['name'], $fish['speed'], $fish['satiety'], $id);
            } else {
                throw new \InvalidArgumentException('Вы не указали обязательные поля для Fish ('.implode(', ', $diff).').');
            }
        }
    }

    /**
     * Этот метод я использую когда хочу проверить на обожрались ли там еще рыбки
     *
     * @return int
     */
    public function getSatietySum()
    {
        $sum = 0;
        //К сожалению array_column не работает с приватными полями. Можно было бы, конечно, дописать еще магический метод __isset
        //И тогда должно было бы завестись, но уже не сегодня
        foreach ($this->fishes as $fish) {
            $sum += $fish->getSatiety();
        }
        return $sum;
    }

    /** А сколько всего рыбок в нашем аквариуме?
     *
     * @return int
     */
    public function getCount()
    {
        return count($this->fishes);
    }

    /** Разновидности рыбок
     * Да, не таккая у нашего аквариума и богатая "фауна"
     * @return array
     */
    public function getTypes()
    {
        return [
            self::FISH_TYPE_CARP => 'Займо-карп',
            self::FISH_TYPE_PIKE => 'Кредито-щука',
            self::FISH_TYPE_STURGEON => 'Банко-осётр',
        ];
    }

    /**
     * Связываем разновидности рыб с классами из особенностей
     * @return array
     */
    public function getTypesFeatures()
    {
        return [
            self::FISH_TYPE_CARP => 'game\features\CarpFeatures',
        ];
    }

    /**
     * Сделать ход всем рыбам
     *
     * @return bool
     *
     */
    public function go()
    {
        //Ищем максимум скорости. Если вы понимаете о чем я.

        $winner = reset($this->fishes);
        $max_speed = $winner->move();
        $favorites = [];

        foreach (($fishes = array_slice($this->fishes, 1)) as $fish) {
            $speed = $fish->move();
            if ($speed > $max_speed) {
                $max_speed =  $speed;
                Moves::add($winner->name.' не успевает.');
                $winner = $fish;
            } elseif ($speed == $max_speed) {
                if (!$this->aquarium->stoppedMove()) {
                    Moves::add(sprintf('У %s и %s одинаковая скорость %.2f. Сравниваем сытость...', $fish->name, $winner->name, $speed));
                }
                /*
                 * Эти рыбки живут в утопическом обществе,
                 * в котором не принято хапать потому, что можешь
                 * тем более если тебе и не надо.
                 * По этому орешек достается рыбке, которая больше в нем нуждается.
                 * Ну или потому, что в условии не сказано как быть, если у двух (и более)
                 * рыбок одинаковая скорость.
                */

                if ($fish->satiety < $winner->satiety) {
                    $winner = $fish;
                    if (!$this->aquarium->stoppedMove()) {
                        Moves::add(sprintf('Рыбка %s более голодная, уступаем ей', $fish->name));
                    }
                } elseif ($fish->satiety == $winner->satiety) {

                    /*
                     * Я сдаюсь, пусть решает великий и могучий рандом
                     * Не все же ему призы за репосты в вк разыгрывать
                     */

                    if (!$this->aquarium->stoppedMove()) {
                        Moves::add('У этих рыбок даже аппетит одинаковый. Я сдаюсь, пусть рандом с этим разбирается.');
                    }
                    $favorites[] = $fish;
                }
            } else {
                if (!$this->aquarium->stoppedMove()) {
                    Moves::add($fish->name.' не успевает.');
                }
            }
        }

        if (!empty($favorites)) {
            $winner = $favorites[array_rand($favorites)];
            Moves::add(sprintf('И так. Рандом решил, что победила рыбка %s. Поздравим же ее с этой сложной победой!', $winner->name));
        }
        if ($winner->eat()) {

            if ($this->getSatietySum() == Fish::MAX_SATIETY * count($this->fishes)) {
                Moves::add('Похоже, что все рыбки наелись до отвала. Нужно заканчивать игру, пока они не лопнули.');
            }

            return $this->aquarium->peanutEaten();
        }
        return false;
    }

    /**
     * Эфпять. Все аналогично Game и Aquarium, только для Fishes.
     */
    public function save()
    {
        foreach ($this->fishes as $fish) {  //Надеюсь вы не будете слишком много рыбок в аквариум запускать.
            $fish->save();
        }
    }

    /**
     * Найдем всех жителей нашего аквариума.
     *
     * @param \game\Aquarium $aquarium
     *
     * @return \game\Fishes
     */
    public static function findInAquarium(Aquarium $aquarium)
    {
        $stmt = Db::getConnection()->prepare('select * from `fishes` where `aquarium_id`=?');
        $stmt->execute([$aquarium->getId()]);
        return new Fishes($stmt->fetchAll(), $aquarium);
    }
}