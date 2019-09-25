<?php

namespace DenielWorld\TechMinePM\interfaces;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Spawnable;

abstract class Generator extends Spawnable {

    private $nbt;

    private $inventory = null;

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

    public function getInventory(){
        return $this->inventory;
    }

    public function getRealInventory(){
        return $this->getInventory();
    }

    public function setEnergy(int $kws){
        $this->getNBT()->setInt("energy", $kws);
    }

    public function getNBT(): CompoundTag{
        return $this->nbt;
    }

}