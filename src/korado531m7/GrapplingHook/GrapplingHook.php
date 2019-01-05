<?php
namespace korado531m7\GrapplingHook;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\item\ItemFactory;

class GrapplingHook extends PluginBase{
    private static $fishing = [];
    
    public function onEnable(){
        ItemFactory::registerItem(new FishingRod(), true);
    }
    
    public static function getFishingHook(Player $player) : ?FishingHook{
        return self::$fishing[$player->getName()] ?? null;
    }
    
    public static function setFishingHook(?FishingHook $fish, Player $player){
        self::$fishing[$player->getName()] = $fish;
    }
}