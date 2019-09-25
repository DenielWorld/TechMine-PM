<?php

namespace DenielWorld\TechMinePM\tile;

use DenielWorld\TechMinePM\Main;
use pocketmine\inventory\InventoryHolder;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Container;
use pocketmine\tile\Nameable;
use pocketmine\tile\Spawnable;

class Wire extends Spawnable implements InventoryHolder, Container, Nameable
{

    private $nbt;

    private $plugin;

    public function __construct(Main $plugin, Level $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);
        //no inventory for u hehe
        $this->plugin = $plugin;
        $this->scheduleUpdate();
    }

    public function setName(string $str)
    {
        $this->getInventory()->setName($str);
    }

    public function getName(): string
    {
        return $this->getInventory()->getName();
    }

    public function hasName(): bool
    {
        return $this->getInventory()->getName() !== null ?? false;
    }

    public function getDefaultName(): string
    {
        return "Wire";
    }

    public function addAdditionalSpawnData(CompoundTag $nbt): void
    {
        // still dont know how to do this/what this does
    }

    public function canOpenWith(string $key): bool
    {
        return false;//what is there to open?
    }

    public function onUpdate(): bool
    {
        /*$bm = $this->plugin->getBlockManager();
        $wire = $bm->getConnectedWires($this->getBlock(), $this->asVector3());
        if($wire !== $this->getBlock()){
            $machines = $bm->getConnectedMachines($wire);
            foreach ($machines as $machine){
                if($machine->asVector3() !== $this->asVector3() and $machine instanceof Machine){
                    $tile = $this->getLevel()->getTile($machine->asVector3());
                    $tile->getCleanedNBT()->setInt("energy", $tile->getCleanedNBT()->getInt("energy") + how am i gonna get dis?);
                    return true;
                }
            }
        }*/
        return true;
    }

    public function getConnectedMachines() : array{
        $block = $this;
        $pos = $this->asVector3();
        $machines = [$block->getLevel()->getBlockAt($pos->x, $pos->y - 1, $pos->z),
            $block->getLevel()->getBlockAt($pos->x, $pos->y + 1, $pos->z),
            $block->getLevel()->getBlockAt($pos->x - 1, $pos->y, $pos->z),
            $block->getLevel()->getBlockAt($pos->x + 1, $pos->y, $pos->z),
            $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z - 1),
            $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z + 1)
        ];
        $return = [];
        foreach ($machines as $machine){
            if($machine instanceof Machine){//todo machine instance, I swear dis will drive meh nuts
                array_push($return, $machine);
            }
        }
        return $return;
    }

    public function getInventory()
    {
        return null;
    }

    public function getRealInventory()
    {
        return null;
    }

}
