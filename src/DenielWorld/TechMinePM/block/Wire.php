<?php

namespace DenielWorld\TechMinePM\block;

use DenielWorld\TechMinePM\Main;
use pocketmine\block\EndRod;
use pocketmine\block\Solid;

class Wire extends EndRod {

    private $plugin;

    public function __construct(Main $plugin, int $meta = 0)
    {
        parent::__construct($meta);
        $this->plugin = $plugin;
    }

    public function ticksRandomly(): bool
    {
        return true;
    }

    public function onRandomTick(): void
    {
        $this->plugin->getBlockManager()->updateAroundConnections($this);
    }
}