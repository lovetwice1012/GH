<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace korado531m7\GrapplingHook;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AnimatePacket;

class FishingRod extends Tool{

    public function __construct(){
        parent::__construct(self::FISHING_ROD, 0, 'Fishing Rod');
    }

    public function getMaxStackSize() : int{
        return 1;
    }

    public function getMaxDurability() : int{
        return 65;
    }
    
    public function getFuelTime() : int{
        return 300;
    }
    
    public function onAttackEntity(Entity $victim) : bool{
        return $this->applyDamage(1);
    }

    public function onClickAir(Player $player, Vector3 $directionVector) : bool{
        if(GrapplingHook::getFishingHook($player) === null){
            $nbt = Entity::createBaseNBT($player);
            $hook = Entity::createEntity('FishingHook', $player->level, $nbt, $player);
            $hook->spawnToAll();
        }else{
            $hook = GrapplingHook::getFishingHook($player);
            $hook->handleHookRetraction();
        }
        $player->broadcastEntityEvent(AnimatePacket::ACTION_SWING_ARM);
        return true;
    }
}
