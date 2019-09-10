<?php

namespace DenielWorld\TechMinePM\managers;

use DenielWorld\TechMinePM\Main;
use pocketmine\block\Block;
use pocketmine\math\Vector3;

class BlockManager{

    private $type;

    private $plugin;

    public function __construct(Main $plugin, string $type = "BlockManager")
    {
        $this->type = "BlockManager";
        $this->plugin = $plugin;
    }

    public function getConnectedWires(Block $block, Vector3 $pos) : Block{//returns where the connected wires lead lel
        $wires = [$block->getLevel()->getBlockAt($pos->x, $pos->y - 1, $pos->z),
                    $block->getLevel()->getBlockAt($pos->x, $pos->y + 1, $pos->z),
                    $block->getLevel()->getBlockAt($pos->x - 1, $pos->y, $pos->z),
                    $block->getLevel()->getBlockAt($pos->x + 1, $pos->y, $pos->z),
                    $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z - 1),
                    $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z + 1)
                 ];
        //[below block, above block, to the left from block, to the right from block, upper from block, below from block]
        foreach ($wires as $wire){
            if($wire->getId() == Block::END_ROD and $this->hasConnectedWires($wire, $wire->asVector3())){
                $this->getConnectedWires($wire, $wire->asVector3());//creates a loop until the wire chain reaches a final wire destination
            }
            elseif($wire->getId() == Block::END_ROD){
                return $wire;//when the final destination is reached the block for the destination is returned
            }
        }
        return $block;//if there is nothing connected then the block that was passed on as argument 1 is returned back
    }

    public function hasConnectedWires(Block $block, Vector3 $pos) : bool{
        $wires = [$block->getLevel()->getBlockAt($pos->x, $pos->y - 1, $pos->z),
            $block->getLevel()->getBlockAt($pos->x, $pos->y + 1, $pos->z),
            $block->getLevel()->getBlockAt($pos->x - 1, $pos->y, $pos->z),
            $block->getLevel()->getBlockAt($pos->x + 1, $pos->y, $pos->z),
            $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z - 1),
            $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z + 1)
        ];
        foreach ($wires as $wire){
            if($wire->getId() == Block::END_ROD){
                return true;
            }
        }
        return false;
    }

    public function getType()
    {
        return "BlockManager";
    }
}