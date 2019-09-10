<?php
namespace korado531m7\InventoryMenuAPI\utils;

use pocketmine\item\Item;

class TradingRecipe{
    private $ingredient1;
    private $ingredient2 = null;
    private $result;
    
    public function __construct(){
        
    }
    
    /**
     * You must set at least an ingredient 
     */
    public function setIngredient(Item $item){
        $this->ingredient1 = $item;
    }
    
    public function setIngredient2(Item $item){
        $this->ingredient2 = $item;
    }
    
    public function setResult(Item $item){
        $this->result = $item;
    }
    
    public function getIngredient() : Item{
        return $this->ingredient1;
    }
    
    public function getIngredient2() : ?Item{
        return $this->ingredient2;
    }
    
    public function getResult() : Item{
        return $this->result;
    }
}