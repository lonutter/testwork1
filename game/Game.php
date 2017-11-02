<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game;


use game\interfaces\GameInterface;
use PDO;

/**
 * Class Game
 *  Управляет ходом игры через класс Round
 * @package game
 */
class Game implements \Iterator, \ArrayAccess, GameInterface
{
    private
        $rounds = [],
        $round = 1,
        $aquarium,
        $id = null,
        $num_peanuts;

    /**
     * Game constructor.
     *
     * @param \game\Aquarium $aq
     * @param int $num_peanuts
     */
    public function __construct(Aquarium $aq, $num_peanuts = 2)
    {
        $this->aquarium = $aq;    //Аквариум

        //Что бы поиграть нужно 1 и более орехов
        if (is_int($num_peanuts) && $num_peanuts > 0) {
            $this->num_peanuts = $num_peanuts;
        } else {
            throw new \InvalidArgumentException('Количество орехов должно быть целым числом и больше 0.');
        }

        Moves::newGame($this);
    }

    /**
     * Геттер для приватных свойств объекта
     * @param string $name Имя свойства объекта
     *
     * @return mixed значение свойства объекта
     */
    public function __get($name)
    {
        //Убираем все нижние подчеркивания и заменяем следующую за ними букву на верхний регистр
        if (strpos($name, '_')) {
            $name_parts = explode('_', $name);
            $method_name = 'get'.implode('', array_map('ucfirst', $name_parts));
        } else {
            $method_name = 'get'.ucfirst($name);
        }
        return method_exists($this, $method_name) ? $this->$method_name() : null;
    }

    /**
     * Сеттер приватных свойств
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        //Разумеется здесь такой же принцип как и геттере
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
     * @return null|int Идентификатор игры
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \game\Aquarium
     */
    public function getAquarium()
    {
        return $this->aquarium;
    }

    /**
     * @return int Количество орехов в игре
     */
    public function getNumPeanuts()
    {
        return $this->num_peanuts;
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
     * @param $num_peanuts
     */
    public function setNumPeanuts($num_peanuts)
    {
        if (is_int($num_peanuts) && $num_peanuts > 0) {
            $this->num_peanuts = $num_peanuts;
        }
    }

    /**
     * Загружает игру из БД по ее ID
     *
     * @param $id
     *
     * @return \game\Game
     */
    public static function load($id)
    {
        if (is_int($id) && $id > 0) {
            $stmt = Db::getConnection()->prepare('select * from `games` where `id`=:id');
            $stmt->bindValue('id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $game_data = $stmt->fetch();

            if (empty($game_data)) {
                throw new \InvalidArgumentException("Игра с идентификатором $id не найдена.");
            }

            $game = new Game(Aquarium::load($game_data['aquarium_id']), $game_data['num_peanuts']);
            $game->id = $game_data['id'];

            return $game;
        } else {
            throw new \InvalidArgumentException('Параметр $id должен быть целым числом больше 0.');
        }
    }

    /**
     * Сохраняет игру в БД
     * @return bool
     */
    public function save()
    {
        if (is_null($this->id)) {
            $this->aquarium->save();
            $stmt = Db::getConnection()->prepare('insert into `games` (`aquarium_id`, `num_peanuts`) values (:aquarium_id, :num_peanuts)');
        } else {
            $stmt = Db::getConnection()->prepare('update `games` set `aquarium_id`=:aquarium_id, `num_peanuts`=:num_peanuts where `id`=:id');
            $stmt->bindValue('id', $this->id, PDO::PARAM_INT);
        }

        $stmt->bindValue('aquarium_id', $this->aquarium->id, PDO::PARAM_INT);
        $stmt->bindValue('num_peanuts', $this->num_peanuts, PDO::PARAM_INT);
        $result = $stmt->execute();
        Moves::setGame(Db::getConnection()->lastInsertId());
        Moves::save();
        return $result;
    }

    /**
     * Удаляет игру из БД
     * @return bool|null
     */
    public function delete()
    {
        $result = null;

        if (!is_null($this->id)) {
            $stmt = Db::getConnection()->prepare('delete from `games` where `id`=:id');
            $stmt->bindValue('id', $this->id, PDO::PARAM_INT);
            $result = $stmt->execute();
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getRoundId()
    {
        return $this->round;
    }

    /**
     * @param int $id
     *
     * @return mixed|null
     */
    public function getRound($id = 1)
    {
        return array_key_exists($id, $this->rounds) ? $this->rounds[$id] : null;
    }

    /**
     * Текущий орех съеден. Уменьшаем общее количество орехов/
     */
    public function peanutEaten()
    {
        $this->num_peanuts--;

        if ($this->num_peanuts == 0) {
            Moves::add('Мне неловко об этом говорить, но у нас закончились орехи.');
        }
    }

    /**
     * Сброс указателя массива
     */
    public function rewind()
    {
        $this->round = 1;
    }

    /**
     * Создает новый раунд и запускает его.
     *
     * @return \game\Round Новый раунд
     */
    public function current()
    {
        $round = new Round($this);
        $round->setId($this->round);
        $round->play();

        $this->rounds[$this->round] = $round;

        return $this->rounds[$this->round];
    }

    /**
     * Возвращает ключ текущего элемента массива
     *
     * @return int|null|string Ключ текущего элемента массива
     */
    public function key()
    {
        return $this->round;
    }

    /**
     * Инкрементирует порядковый номер раунда
     *
     */
    public function next()
    {
        $this->round++;
    }

    /**
     * Валидация элемента массива по ключу
     *
     * @return bool Результат проверки
     */
    public function valid() {
        return $this->offsetExists($this->round);
    }

    /**
     * @param mixed|string $index
     *
     * @return bool
     */
    public function offsetExists($index)
    {
        //Тут мы смотрим не наелись ли рыбки и если наелись, то прекращаем игру
        $fishes = $this->getAquarium()->getFishes();
        $sum = $fishes->getSatietySum();
        if ($sum == Fish::MAX_SATIETY * $fishes->getCount()) {
            Moves::add('Похоже, что все рыбки наелись до отвала. Нужно заканчивать игру, пока они не лопнули.');
            $this->num_peanuts = 0;
        }
        return $this->num_peanuts > 0;
    }

    /**
     * @param mixed|string $index
     *
     * @return mixed|null
     */
    public function offsetGet($index) {
        return isset($this->rounds[$index]) ? $this->rounds[$index] : null;
    }

    /**
     * @param mixed|string $index
     * @param mixed|string $newval
     */
    public function offsetSet($index, $newval) {
        if (is_null($index)) {
            $this->rounds[] = $newval;
        } else {
            $this->rounds[$index] = $newval;
        }
    }

    /**
     * @param mixed|string $index
     */
    public function offsetUnset($index) {
        unset($this->rounds[$index]);
    }
}