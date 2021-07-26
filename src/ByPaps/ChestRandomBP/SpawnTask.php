<?php

/***
 * *        _|\____________________,,__
 *       / `--|||||||||||-------------_] Plugin by Dev ByPaps
 *      /_==o ____________________|
 *        ),---.(_(__) /
 *       // (\) )
 *      //___/ /
 *     /`----'/
 *    /____ / 
 *  This Plugin Was Developed By Empresas BP de S.A de C.V
 *  The use of this plugin is private, until the moment it is made public, but please do not modify the Author of the same.
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Autor: Dev ByPaps
 *  YouTube: Developer ByPaps
 *  GitHub: ByPaps
 *
 */

namespace ByPaps\ChestRandomBP;
use pocketmine\item\Item;
use pocketmine\scheduler\Task;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Tile;
use pocketmine\nbt\NBT;
use pocketmine\block\Block;
use pocketmine\nbt\tag\NamedTag;
class SpawnTask extends Task
{
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick)
    {
        if(count($this->plugin->chests) === $this->plugin->config->get("maximum_chests")) return;
        $worlds = $this->plugin->config->get("worlds");
        foreach ($worlds as $world) {
            $level = $this->plugin->getServer()->getLevelByName($world["name"]);
            $array = $this->getRandomWorldSpawn($world["name"]);
            $level_name = $world["name"];
            if ($array) {
                $block = $level->getBlock(new Vector3($array[0], $array[1], $array[2]));
                $x = $array[0];
                $y = $array[1];
                $z = $array[2];
                $id = $block->getItemId();
                if($id !== 9 && $id !== 0) {
                    $block = Block::get(54);
                    $level->setBlock(new Vector3($array[0], $array[1] + 1, $array[2]), $block, true, true);
                    $nbt = new CompoundTag("", [
                        new ListTag("Items", []),
                        new StringTag("id", Tile::CHEST),
                        new IntTag("x", $array[0]),
                        new IntTag("y", $array[1] + 1),
                        new IntTag("z", $array[2])
                    ]);
                    $nbt->getTag("Items")->setTagType(NBT::TAG_Compound);
                    $tile = Tile::createTile("Chest", $level, $nbt);
                    $inv = $tile->getInventory();
                    for($i = 0; $i < $this->plugin->config->get("maximum_items_per_chest"); $i++){
                        $array = $this->plugin->items->get("items");
                        $item_string = array_rand($this->plugin->items->get("items"));
                        $item_string = $array[$item_string];
                        $ar = explode(":", $item_string);
                        $item = Item::get($ar[0], $ar[1], $ar[2]);
                        $inv->setItem($i, $item);
                    }
                    $y++;
                    $this->plugin->chests["$x:$y:$z:$level_name"] = "";
                }
            }
        }
    }
    public function getRandomWorldSpawn($world_name){
        $worlds = $this->plugin->config->get("worlds");
        foreach($worlds as $world){
            if($world["name"] === $world_name){
                $level = $this->plugin->getServer()->getLevelByName($world["name"]);
                $x = mt_rand($world["min_x"], $world["max_x"]);
                $z = mt_rand($world["min_z"], $world["max_z"]);
                $y = $level->getHighestBlockAt($x + 1, $z + 1);
                if($y === -1){
                    return null;
                }
                return [$x, $y, $z];
            }
        }
        return null;
    }
}
