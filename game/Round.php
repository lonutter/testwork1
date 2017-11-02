<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game;

use game\interfaces\RoundInterface;

/**
 * Class Round
 *
 * @package game
 */
class Round implements RoundInterface
{
    private
        $id,
        $game;

    /**
     * Round constructor.
     *
     * @param \game\Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game;
        Moves::newRound();
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Возвращает список ходов в этом раунде.
     *
     * @return array|mixed|null
     */
    public function getMoves()
    {
        return Moves::get($this->id);
    }

    /**
     *  Выполняет ход
     */
    public function play()
    {
        //Если в аквариуме уже есть орешек
        if ($this->game->getAquarium()->hasPeanut()) {
            //Морские котики, go-go-go
            if ($this->game->getAquarium()->getFishes()->go()) {
                //Если мы оказались тут, то значит орешек всё. Кончился.
                $this->game->peanutEaten();
            }
        } else {
            //А вот это говорит о том, что либо это первый раунд и орешек еще не кинули в аквариум,
            //либо его уже съели.
            //Как бы там ни было, а нужно кидать новый. Если есть, конечно.
            $this->game->getAquarium()->putPeanut(new Peanut());
        }
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}