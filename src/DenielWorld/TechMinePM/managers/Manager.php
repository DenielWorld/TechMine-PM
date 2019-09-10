<?php

namespace DenielWorld\TechMinePM\managers;

use DenielWorld\TechMinePM\Main;
use http\Exception\InvalidArgumentException;

class Manager{

    protected $type;

    private $plugin;

    public function __construct(Main $plugin, string $type)
    {
        $this->type = $type;
        $this->plugin = $plugin;
        if($type == "BlockManager"){
            return new BlockManager($plugin, $type);
        }
        else {
            throw new InvalidArgumentException("Non-existing manager type was provided as the second argument");
        }
    }

    public function getType(){
        return $this->type;
    }

}