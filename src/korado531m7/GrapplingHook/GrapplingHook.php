<?php
namespace korado531m7\GrapplingHook;

use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\plugin\PluginBase;

class GrapplingHook extends PluginBase implements Listener{
    private static $fishing = [];
    
    public function onEnable(){
        ItemFactory::registerItem(new FishingRod(), true);
        Entity::registerEntity(FishingHook::class, false, ['FishingHook', 'minecraft:fishinghook']);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    
    public function onDamage(EntityDamageEvent $event){
        $player = $event->getEntity();
        if(!$player instanceof Player || $event->getCause() !== EntityDamageEvent::CAUSE_FALL) return true;
        if($player->getInventory()->getItemInHand()->getId() === ItemIds::FISHING_ROD){
            $event->setCancelled();
        }
    }
    
    public static function getFishingHook(Player $player) : ?FishingHook{
        return self::$fishing[$player->getName()] ?? null;
    }
    
    public static function setFishingHook(?FishingHook $fish, Player $player){
        self::$fishing[$player->getName()] = $fish;
    }
}