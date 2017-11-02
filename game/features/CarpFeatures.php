<?php
/**
 * Created by PhpStorm.
 * User: lonutter
 * Date: 01.11.2017
 */

namespace game\features;


use game\Fish;

class CarpFeatures extends FishFeatures
{
    public function getEvents()
    {
        return [
            Fish::BEFORE_EAT_EVENT => [
                [
                    'callback' => function (Fish $fish, $args = []) {
                        if ($fish->canEat()) {
                            if (($rand = mt_rand(1, 10)) < 4) {
                                $fish->blockEat($args['num_moves']);
                            }
                        } elseif (!$fish->canEat() && $fish->getBlockedEats() > 0) {
                            $fish->decrementBlockedEats();
                        } else {
                            $fish->unblockEat();
                        }
                    },
                    'args' => [
                        'num_moves' => 5,
                    ]
                ]
            ],
        ];
    }
}