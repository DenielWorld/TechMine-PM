<?php

namespace DenielWorld\TechMinePM\tasks;

use DenielWorld\TechMinePM\Main;
use pocketmine\scheduler\Task;

class CoalGeneratorUpdateTask extends Task{

    private $plugin;

    private $data;

    public function __construct(Main $plugin, string $nested_value)
    {
        $this->plugin = $plugin;
        $this->data = $nested_value;
    }

    public function onRun(int $currentTick)
    {
        // TODO: Get started
    }
}