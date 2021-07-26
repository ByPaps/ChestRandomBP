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
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
class Main extends PluginBase {
    public $config;
    public $items;
    public $chests = [];
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new BPListener($this), $this);
        @mkdir($this->getDataFolder());
        $this->saveResource("/config.yml");
        $this->saveResource("/items.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->items = new Config($this->getDataFolder() . "items.yml", Config::YAML);
        $this->getScheduler()->scheduleDelayedRepeatingTask(new SpawnTask($this), $this->config->get("spawn_interval") * 20, $this->config->get("spawn_interval") * 20); //delay, period/interval
    }
    public function onDisable()
    {
        $keys = array_keys($this->chests);
        foreach ($keys as $chest){
            $array = explode(":", $chest);
            $vector = new Vector3($array[0], $array[1], $array[2]);
            $level = $this->getServer()->getLevelByName($array[3]);
            $tile = $level->getTile($vector);
            if($tile){
                $tile->close();
                $level->setBlock($vector, new Block(0));
            }
        }
    }
}
