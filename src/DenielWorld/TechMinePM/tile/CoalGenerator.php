<?php

namespace DenielWorld\TechMinePM\tile;

use DenielWorld\TechMinePM\interfaces\Generator;
use korado531m7\InventoryMenuAPI\inventory\HopperInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Container;
use pocketmine\tile\Nameable;

class CoalGenerator extends Generator implements InventoryHolder, Container, Nameable{

    private $nbt;

    private $inventory = null;

    public function __construct(Level $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);
        $coal_inv = new HopperInventory();
        $coal_inv->setName("Coal Generator");
        $coal_inv->setContents([1 => Item::get(Item::GLASS_PANE, 4, 1)->setCustomName("<= Battery Slot"),
            2 => Item::get(465, 0, 1)->setCustomName("Stored Energy: " . $this->getNBT()->getInt("energy")),
            3 => Item::get(Item::GLASS_PANE, 4, 1)->setCustomName("Coal Slot =>")
        ]);
        $this->inventory = $coal_inv;
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
        return "Coal Generator";
    }

    public function canOpenWith(string $key): bool
    {
        return true;
    }

    public function addAdditionalSpawnData(CompoundTag $nbt): void
    {
        //eh
    }

    public function getRealInventory()
    {
        return $this->getInventory();
    }

    public function onUpdate(): bool
    {
        if($this->isClosed()){//todo figure if this can work when closed
            return false;
        }
        if($this->getInventory()->getItem(4)->getId() == Item::COAL){
            $this->setCoal($this->getNBT()->getInt("coal") + $this->getInventory()->getItem(4)->getCount());
            $this->getInventory()->setItem(4, Item::get(0));
        }
        $this->getInventory()->getItem(2)->setCustomName("Stored Energy: " . $this->getNBT()->getInt("energy"));
        //todo transfer energy via connected wires
        //todo battery charging
        return true;
    }

    public function writeSaveData(CompoundTag $nbt): void
    {
        $nbt->setInt("energy", 0);
        $nbt->setInt("coal", 0);
    }

    public function readSaveData(CompoundTag $nbt): void
    {
        $this->nbt = $nbt;
    }

    public function getNBT(): CompoundTag{
        return $this->nbt;
    }

    public function getInventory(){
        return $this->inventory;
    }

    public function setEnergy(int $kws){
        $this->getNBT()->setInt("energy", $kws);
    }

    public function setCoal(int $count){
        $this->getNBT()->setInt("coal", $count);
    }
}
