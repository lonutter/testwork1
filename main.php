<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

require 'game/config/autoloader.php';
$config = require('game/config/config.php');

use \game\Game;
use \game\Aquarium;
use \game\Fishes;
use \game\Config;

Config::init($config);  //Загружаем конфигурацию. В ней доступы к БД

//Рыбки
$fishes = [
    [
        'type' => Fishes::FISH_TYPE_PIKE,
        'name' => 'Кредито-щука Ира',
        'speed' => 3,
        'satiety' => 5,
    ],
    [
        'type' => Fishes::FISH_TYPE_CARP,
        'name' => 'Займо-карп Алина',
        'speed' => 7,
        'satiety' => 2,
    ],
    [
        'type' => Fishes::FISH_TYPE_STURGEON,
        'name' => 'Банко-осётр Марина',
        'speed' => 6,
        'satiety' => 3,
    ],
    [
        'type' => Fishes::FISH_TYPE_PIKE,
        'name' => 'Кредито-щука Вера',
        'speed' => 5,
        'satiety' => 4,
    ],
    [
        'type' => Fishes::FISH_TYPE_STURGEON,
        'name' => 'Банко-осётр Евгения',
        'speed' => 1,
        'satiety' => 2,
    ],
];

//Аквариум
$aquarium = new Aquarium();
$aquarium->setName('Аквариум I');
$aquarium->putFishes(new Fishes($fishes, $aquarium));

//Стартуем игру
foreach (($game = new Game($aquarium, 8)) as $round) {
    foreach ($moves = $round->getMoves() as $move) {
        echo "$move\n";
    }
}

//Сохраняем
$game->save();