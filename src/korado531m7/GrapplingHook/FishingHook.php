<?php
/*
 *               _ _
 *         /\   | | |
 *        /  \  | | |_ __ _ _   _
 *       / /\ \ | | __/ _` | | | |
 *      / ____ \| | || (_| | |_| |
 *     /_/    \_|_|\__\__,_|\__, |
 *                           __/ |
 *                          |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TuranicTeam
 * @link https://github.com/TuranicTeam/Altay
 *
 */
 
namespace korado531m7\GrapplingHook;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\entity\projectile\Projectile;
use pocketmine\utils\Random;

class FishingHook extends Projectile{

    public const NETWORK_ID = self::FISHING_HOOK;

    public $height = 0.25;
    public $width = 0.25;
    protected $gravity = 0.1;
    
    public function __construct(Level $level, CompoundTag $nbt, ?Entity $owner = null){
        parent::__construct($level, $nbt, $owner);
        $this->random = new Random();
        if($owner instanceof Player){
            $this->setPosition($this->add(0, $owner->getEyeHeight() - 0.1));
            $this->setMotion($owner->getDirectionVector()->multiply(0.4));
            GrapplingHook::setFishingHook($this, $owner);
            $this->handleHookCasting($this->motion->x, $this->motion->y, $this->motion->z, 1.5, 1.0);
        }
    }

    public function onHitEntity(Entity $entityHit, RayTraceResult $hitResult) : void{
        //Do nothing
    }

    public function canBePushed() : bool{
        return false;
    }

    public function handleHookCasting(float $x, float $y, float $z, float $f1, float $f2){
        $f = sqrt($x * $x + $y * $y + $z * $z);
        $x = $x / (float) $f;
        $y = $y / (float) $f;
        $z = $z / (float) $f;
        $x = $x + $this->random->nextSignedFloat() * 0.007499999832361937 * (float) $f2;
        $y = $y + $this->random->nextSignedFloat() * 0.007499999832361937 * (float) $f2;
        $z = $z + $this->random->nextSignedFloat() * 0.007499999832361937 * (float) $f2;
        $x = $x * (float) $f1;
        $y = $y * (float) $f1;
        $z = $z * (float) $f1;
        $this->motion->x += $x;
        $this->motion->y += $y;
        $this->motion->z += $z;
    }

    public function entityBaseTick(int $tickDiff = 1) : bool{
        $hasUpdate = parent::entityBaseTick($tickDiff);
        $owner = $this->getOwningEntity();
        if($owner instanceof Player){
            if(!$owner->getInventory()->getItemInHand() instanceof FishingRod or !$owner->isAlive() or $owner->isClosed()) $this->flagForDespawn();
        }else $this->flagForDespawn();

        return $hasUpdate;
    }

    public function close() : void{
        parent::close();

        $owner = $this->getOwningEntity();
        if($owner instanceof Player){
            GrapplingHook::setFishingHook(null, $owner);
        }
    }

    public function handleHookRetraction() : void{
        $owner = $this->getOwningEntity();
        $dist = $this->distance($owner);
        $owner->setMotion($this->subtract($owner)->multiply($this->getGrapplingSpeed($dist)));
        $this->flagForDespawn();
    }
    
    private function getGrapplingSpeed(float $dist) : float{
        $a = $dist / 6;
        $limit = 0.253781903; //limit motion for detecting cheat and good speed
        return $a >= $limit ? $limit : $a;
    }

    public function applyGravity() : void{
        if($this->isUnderwater()) $this->motion->y += $this->gravity;
            else parent::applyGravity();
    }
}