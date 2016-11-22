<?php
/**
 * Created by PhpStorm.
 * User: gilko.nikolai
 * Date: 21.11.2016
 * Time: 16:48
 */

namespace common\models;


use common\models\combinations\BaseCombination;
use yii\base\Model;

class Table extends Model
{

    /**
     * @var Player[]
     */
    public $players = [];

    /**
     * @var Deck
     */
    public $deck;

    /**
     * @var Card
     */
    public $cards = [];

    /**
     * @var BaseCombination[]
     */
    public $combinations = [];

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        foreach($this->players as $player){
            $player->takeCard($this->deck);
        }

        $this->cards[] = $this->deck->getCard();

        foreach($this->players as $player){
            $player->takeCard($this->deck);
        }

        for($i = 0; $i <= 3; $i++){
            $this->cards[] = $this->deck->getCard();
        }
    }

    public function getWinCombinations(){
        $this->checkCombinations();

        if(!$this->combinations){
            return [];
        }

        ksort($this->combinations);

        $combinations = [];

        foreach(array_shift($this->combinations) as $combination){
            if(!$combinations[$combination->value]){
                $combinations[$combination->value] = [];
            }

            $combinations[$combination->value][] = $combination;
        }

        krsort($combinations);

        return array_shift($combinations);
    }

    /**
     *
     */
    public function checkCombinations(){
       foreach($this->players as $player){
            foreach($this->getPossibleCombinations() as $weight => $combination){
                $combination = new $combination([
                    'player'    =>  $player->id,
                    'cards'     =>  array_merge($player->cards, $this->cards)
                ]);

                if($combination->check()){
                    if(!$this->combinations[$weight]){
                        $this->combinations[$weight] = [];
                    }

                    $this->combinations[$weight][] = $combination;

                    break;
                }
            }
       }
    }

    public function getPossibleCombinations(){
        return [
            'common\models\combinations\RoyalFlush',
            'common\models\combinations\Four',
            'common\models\combinations\FullHouse',
            'common\models\combinations\Flush',
            'common\models\combinations\Straight',
            'common\models\combinations\Three',
            'common\models\combinations\TwoPairs',
            'common\models\combinations\Pair',
            'common\models\combinations\HighCard',
        ];
    }

}