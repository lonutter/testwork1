<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game;


use game\interfaces\FishInterface;
use PDO;

/**
 * Class Fish
 *
 * @package game
 */
class Fish implements FishInterface
{
    //Названия доступных событий
    const BEFORE_EAT_EVENT = 'beforeEat';
    const BEFORE_MOVE_EVENT = 'beforeMove';
    const BEFORE_VALIDATE_EAT_EVENT = 'beforeValidateEat';
    const BEFORE_VALIDATE_MOVE_EVENT = 'beforeValidateMove';
    const AFTER_EAT_EVENT = 'afterEat';
    const AFTER_MOVE_EVENT = 'afterMove';
    const AFTER_VALIDATE_MOVE_EVENT = 'afterValidateMove';
    const AFTER_VALIDATE_EAT_EVENT = 'afterValidateEat';

    //Ограничения для свойств
    const MIN_SATIETY = 1;
    const MAX_SATIETY = 10;
    const MIN_SPEED = 0.01;
    const MAX_SPEED = 10;

    //Свойства объекта
    private
        $id = null,
        $type = 0,
        $name = 'fish',
        $speed = null,
        $satiety = null,
        $aquarium = null,
        $events = [],
        $blocked_moves = 0,
        $blocked_eats = 0,
        $canEat = true,
        $canMove = true;

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
    public function __construct($type, $name, $speed, $satiety, Aquarium $aquarium, array $events = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->speed = $speed;
        $this->satiety = $satiety;
        $this->aquarium = $aquarium;

        $this->addEvents($events);
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
     * Сколько там еще осталось раундов до разблокировки? Ответ скажет этот метод.
     *
     * @return int
     */
    public function getBlockedMoves()
    {
        return $this->blocked_moves;
    }

    /**
     * Согласен, странное название. Как и многое другое в этом задании :-)
     * @return int
     */
    public function getBlockedEats()
    {
        return $this->blocked_eats;
    }

    /**
     * @return \game\Aquarium|null
     */
    public function getAquarium()
    {
        return $this->aquarium;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string Разновидность (тип) рыбы
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int Скорость рыбы
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * @return int  Сытость рыбы
     */
    public function getSatiety()
    {
        return $this->satiety;
    }

    /**
     *  Высчитывает скорость во врямя хода
     */
    public function calculateSpeed()
    {
        if ($this->satiety < 6) {
            $this->setSpeed(round($this->speed + $this->speed / $this->satiety, 2));
        }
    }

    /**
     * Уменьшает на единицу количество оставшихся раундов блока
     */
    public function decrementBlockedEats()
    {
        Moves::add(
            sprintf('Осталось подождать всего %d раундов', $this->blocked_eats)
        );
        $this->blocked_eats--;
    }

    /**
     * Увеличивает. Им мы не пользуемся, просто что бы вы знали что еще и так можно :-)
     */
    public function incrementBlockedEats()
    {
        $this->blocked_eats++;
    }

    /**
     * Аналогично *Eats
     */
    public function decrementBlockedMoves()
    {
        $this->blocked_moves--;
    }

    /**
     * Аналогично *Eats
     */
    public function incrementBlockedMoves()
    {
        $this->blocked_moves++;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        if (is_int($id) && $id > 0) {
            $this->id = $id;
        }
    }

    /**
     * Устанавливаем скорость рыбы
     *
     * @param $speed int Скорость рыбы
     */
    public function setSpeed($speed)
    {
        if ($this->validateSpeed($speed)) {
            $this->speed = $speed;
        } elseif ($speed >= self::MAX_SPEED) {
            $this->speed = self::MAX_SPEED;
        }
        else {
            throw new \InvalidArgumentException('Скорость должна быть целым числом в диапазоне от 1 до 10.');
        }
    }

    /**
     * Устанавливает сытость рыбы
     *
     * @param $satiety  int Сытость рыбы
     */
    public function setSatiety($satiety)
    {
        if ($this->validateSatiety($satiety)) {
            $this->satiety = $satiety;
        } else {
            throw new \InvalidArgumentException('Сытость должна быть целым числом в диапазоне от 1 до 10.');
        }
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Можно было бы написать один общий метод-валидатор и работать через правила. Но может быть в другой раз.
     *
     * @param $speed
     *
     * @return bool
     */
    private function validateSpeed($speed)
    {
        return is_numeric($speed) && $speed >= self::MIN_SPEED && $speed <= self::MAX_SPEED;
    }

    /**
     * Валидация сытости рыбы
     *
     * @param $satiety
     *
     * @return bool
     */
    private function validateSatiety($satiety)
    {
        return is_int($satiety) && $satiety >= self::MIN_SATIETY && $satiety <= self::MAX_SATIETY;
    }

    /**
     * Добавляет список обработчиков событий
     *
     * @param array $events Массив обработчиков
     */
    public function addEvents(array $events)
    {
        foreach ($events as $type => $type_events) {
            if (is_array($type_events)) {
                foreach ($type_events as $index => $event) {
                    $args = [];
                    if (array_key_exists('callback', $event)) {
                        $callback = $event['callback'];
                    } else {
                        throw new \InvalidArgumentException("Не найден callback для события $type($index).");
                    }
                    if (array_key_exists('args', $event)) {
                        $args = is_array($event['args']) ? $event['args'] : [];
                    }

                    $this->addEvent($type, $callback, $args);
                }
            } else {
                throw new \InvalidArgumentException('$events должен быть многомерным массивом.');
            }
        }
    }

    /**
     * Добавляет обработчик события
     *
     * @param string $type Тип события
     * @param callable $callback Обработчик события
     * @param array $args Список аргументов для обработчика события $callback
     */
    public function addEvent($type, callable $callback, array $args = [])
    {
        if ($callback instanceof \Closure) {
            $callback->bindTo($this);       //Можем себе позволить не только привязать, но еще и передать в качестве параметра, но об этом ниже.
        }

        $this->events[$type][] = [$callback, $args];
    }

    /**
     * Хватит жрать.
     *
     * @param int $num_rounds
     */
    public function blockEat($num_rounds = 1)
    {
        $this->blocked_eats = $num_rounds;
        $this->canEat = false;
        Moves::add(
            sprintf(
                'О нет! Судьба злодейка! Бедная %s забыла как жевать и чтобы вспомнить ей потребуется целых %d ходов! Давайте же наберемся терпения и будем к ней снисходительны. Она же рыбка, ну в самом деле!',
                $this->name,
                $num_rounds
            )
        );
        $this->aquarium->stopMove();
    }

    /**
     * Ну если только чуть-чуть и не на ночь.
     */
    public function unblockEat()
    {
        $this->blocked_eats = 0;
        $this->canEat = true;
        Moves::add(
            sprintf('"Ура! Я вспомнила как жевать!" - сказала %s и сожрала нахрен целый орех!', $this->name)
        );
        $this->aquarium->continueMove();
    }

    /**
     *  Вызывает коллбеки для события
     */
    protected function beforeValidateEat()
    {
        if (array_key_exists(self::BEFORE_VALIDATE_EAT_EVENT, $this->events)) {
            foreach (($events = $this->events[self::BEFORE_VALIDATE_EAT_EVENT]) as $index => list($callback, $args)) {
                if (empty($args)) {
                    $callback($this);
                } else {
                    $callback($this, $args);
                }
            }
        }
    }

    /**
     * Проверяет может ли рыба съесть орешек
     *
     * @return bool Может ли рыба съесть орешек
     */
    public function canEat()
    {
        $this->beforeValidateEat();
        $result = $this->canEat && $this->aquarium->canEat($this->id) && ($this->satiety + $this->aquarium->getPeanut()->satiety) <= self::MAX_SATIETY;
        $this->afterValidateEat($result);
        return $result;
    }

    /**
     * @param $result
     */
    protected function afterValidateEat($result)
    {
        if (array_key_exists(self::AFTER_VALIDATE_EAT_EVENT, $this->events)) {
            foreach (($events = $this->events[self::AFTER_VALIDATE_EAT_EVENT]) as $index => list($callback, $args)) {
                if (empty($args)) {
                    $callback($this, $result);
                } else {
                    $callback($this, $result, $args);
                }
            }
        }
    }

    /**
     * Тоже обработчик события. Их тут несколько.
     */
    protected function beforeEat()
    {
        if (array_key_exists(self::BEFORE_EAT_EVENT, $this->events)) {
            foreach (($events = $this->events[self::BEFORE_EAT_EVENT]) as $index => list($callback, $args)) {
                if (empty($args)) {
                    $callback($this);   ///Вооот...
                } else {
                    $callback($this, $args);
                }
            }
        }
    }

    /**
     * Ест орех.
     * Добавляет к своей сытости сытость ореха.
     *
     * @return bool
     */
    public function eat()
    {
        $peanut = $this->aquarium->getPeanut();

        $this->beforeEat();

        if ($this->canEat()) {
            $this->satiety += $peanut->getSatiety();
            $this->afterEat();
            Moves::add(sprintf('%s ест орешек. Новые статы: скорость - %.2f, сытость - %d', $this->name, $this->speed, $this->satiety));
            return true;    //Спасибо
        } else {
            $this->getAquarium()->reservePeanut($this->id);
        }

        return false;   //Фу как невкусно.
    }

    /**
     * Обработчик события после того как покушал. Можно и поспать.
     */
    protected function afterEat()
    {
        if (array_key_exists(self::AFTER_EAT_EVENT, $this->events)) {

            foreach (($events = $this->events[self::AFTER_EAT_EVENT]) as $index => list($callback, $args)) {
                if (empty($args)) {
                    $callback($this);
                } else {
                    $callback($this, $args);
                }
            }

        }
    }

    /**
     * Ох уж эта странное стремление все заблокировать
     *
     * @param int $num_moves
     */
    public function blockMove($num_moves = 1)
    {
        $this->blocked_moves = $num_moves;
        $this->canMove = false;
    }

    /**
     * Заблокировать наоборот
     */
    public function unblockMove()
    {
        $this->blocked_moves = 0;
        $this->canMove = true;
    }

    /**
     * Оп! Еще один/
     */
    protected function beforeValidateMove()
    {
        if (array_key_exists(self::BEFORE_VALIDATE_MOVE_EVENT, $this->events)) {

            foreach (($events = $this->events[self::BEFORE_VALIDATE_MOVE_EVENT]) as $index => list($callback, $args)) {
                if (empty($args)) {
                    $callback($this);
                } else {
                    $callback($this, $args);
                }
            }
        }
    }

    /**
     * кен
     * @return bool
     */
    public function canMove()
    {
        $this->beforeValidateMove();
        $result = $this->canMove && $this->satiety < self::MAX_SATIETY && !$this->aquarium->stoppedMove();
        $this->afterValidateMove($result);
        return $result;
    }

    /**
     * @param $result
     */
    protected function afterValidateMove($result)
    {
        if (array_key_exists(self::AFTER_VALIDATE_MOVE_EVENT, $this->events)) {

            foreach (($events = $this->events[self::AFTER_VALIDATE_MOVE_EVENT]) as $index => list($callback, $args)) {
                if (empty($args)) {
                    $callback($this, $result);
                } else {
                    $callback($this, $result, $args);
                }
            }
        }
    }

    /**
     *
     */
    protected function beforeMove()
    {
        if (array_key_exists(self::BEFORE_MOVE_EVENT, $this->events)) {

            foreach (($events = $this->events[self::BEFORE_MOVE_EVENT]) as $index => list($callback, $args)) {
                if (empty($args)) {
                    $callback($this);
                } else {
                    $callback($this, $args);
                }
            }
        }
    }

    /**
     * Ура! Наконец-то походим! Да, эти рыбы ходят.
     * @return null
     */
    public function move()
    {
        $this->beforeMove();

        if ($this->canMove()) {
            $this->calculateSpeed();
            Moves::add(sprintf('%s развила скорость %.2f', $this->name, $this->speed));
            $this->afterMove();
        }

        return $this->speed;
    }

    /**
     *
     */
    protected function afterMove()
    {
        if (array_key_exists(self::AFTER_MOVE_EVENT, $this->events)) {

            foreach (($events = $this->events[self::AFTER_MOVE_EVENT]) as $index => list($callback, $args)) {
                if (empty($args)) {
                    $callback($this);
                } else {
                    $callback($this, $args);
                }
            }

        }
    }

    /**
     * Сохранимся
     */
    public function save()
    {
        $connection = Db::getConnection();

        if (is_null($this->id)) {
            $stmt = $connection->prepare('insert into `fishes` (`type`, `name`, `speed`, `satiety`, `aquarium_id`) values (:type, :name, :speed, :satiety, :aquarium_id)');
        } else {
            $stmt = $connection->prepare('update `fishes` set `type`=:type, `name`=:name, `speed`=:speed, `satiety`=:satiety, `aquarium_id`=:aquarium_id where `id`=:id');
            $stmt->bindValue('id', $this->id, PDO::PARAM_INT);
        }
        $stmt->bindValue('type', $this->type, PDO::PARAM_INT);
        $stmt->bindValue('speed', $this->speed, PDO::PARAM_INT);
        $stmt->bindValue('satiety', $this->satiety, PDO::PARAM_INT);
        $stmt->bindValue('aquarium_id', $this->getAquarium()->getId(), PDO::PARAM_INT);
        $stmt->bindValue('name', $this->name);
        $stmt->execute();
    }
}