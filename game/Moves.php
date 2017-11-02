<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game;

use game\interfaces\MovesInterface;


/**
 * Class Moves
 *
 * @package game
 */
class Moves implements \Iterator, \ArrayAccess, MovesInterface
{
    private static
        $instance,
        $moves = [],
        /**
         * @var \game\Game
         */
        $game,
        $game_id = null,
        $round_id = 0,
        $num_moves = 0;

    /**
     * Singleton
     *
     * @return \game\Moves
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Добавляем сообщение
     *
     * @param   string  $message    Сообщение
     */
    public static function add($message)
    {
        self::$moves[self::$round_id][] = $message . '(Раунд #' . self::$round_id . ')';
        ++self::$num_moves;
    }

    /**
     * Получает список ходов в зависимости от переданных параметров
     *
     * @param null|int $round   Порядковый номер (id) раунда
     * @param null|int $move    Порядковый номера хода
     *
     * @return array|mixed|null
     */
    public static function get($round = null, $move = null)
    {
        if (is_null($round) && is_null($move)) {
            return self::$moves;
        } elseif (!is_null($round) && array_key_exists($round, self::$moves)) {
            return self::$moves[$round];
        } elseif (!is_null($move) && (!is_null($round) && array_key_exists($round, self::$moves))) {
            return self::$moves[$round][$move];
        } else {
            return null;
        }
    }

    /**
     * Сохраняет ходы в БД
     */
    public static function save()
    {
        if (!empty(self::$moves)) {
            $sql = 'INSERT INTO `moves` (`game_id`, `round`, `message`) values '.implode(', ', array_fill(0, self::$num_moves, '(?, ?, ?)'));
            $values = [];
            $game_id = self::$game_id;

            foreach (self::$moves as $round_id => $round_moves) {
                foreach ($round_moves as $move) {
                    $values[] = $game_id;
                    $values[] = $round_id;
                    $values[] = $move;
                }
            }

            $connection = Db::getConnection();
            $connection->beginTransaction();
            $connection->prepare($sql)->execute($values);
            $connection->commit();
        }
    }

    /**
     * @param null $round порядковый номер (id) раунда
     *
     * @return int Количество ходов за всю игру или отдельный раунд
     */
    public static function count($round = null) {
         return !is_null($round) && array_key_exists($round, self::$moves) ? self::$num_moves[$round] : self::$num_moves;
    }

    /**
     * Новая игра. мы потом возьмем оттуда ID игры
     * @param \game\Game $game
     */
    public static function newGame(Game &$game)
    {
        self::$game = $game;
    }

    /**
     * @param $id
     */
    public static function setGame($id)
    {
        self::$game_id = $id;
    }

    /**
     *
     */
    public static function newRound()
    {
        ++self::$round_id;
    }

    /**
     * @param $id
     */
    public static function setRound($id)
    {
        if (is_int($id) && $id > 0 && $id < count(self::$moves)) {
            self::$round_id = $id;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $moves = '';

        foreach (self::$moves as $round_moves) {
            $moves .= implode("\n<br>\n", $round_moves);
        }

        return $moves;
    }

    private function __construct() {}
    private function __clone() {}
    private function __sleep() {}
    private function __wakeup() {}

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return self::$moves[self::$round_id];
    }

    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        ++self::$round_id;
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return self::$round_id;
    }

    /**
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->offsetExists(self::$round_id);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        self::$round_id = 0;
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return array_key_exists(self::$round_id, self::$moves);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return self::$moves[self::$round_id];
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset(self::$moves[self::$round_id]);
    }
}