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
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\tile\Chest;

class BPListener implements Listener
{
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onInventoryClose(InventoryCloseEvent $event)
    {
        $block = $event->getInventory()->getHolder();
        if (!$block instanceof Chest) return;
        $string = "" . $block->getFloorX() . ":" . $block->getFloorY() . ":" . $block->getFloorZ() . ":" . $block->getLevel()->getName();
        if (isset($this->plugin->chests[$string])) {
            $level = $block->getLevel();
            $level->setBlock(new Vector3($block->getFloorX(), $block->getFloorY(), $block->getFloorZ()), new Block(0));
            unset($this->plugin->chests[$string]);
        }
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $block = $event->getBlock();
        if ($block->getName() === "Chest") {
            $string = "" . $block->getFloorX() . ":" . $block->getFloorY() . ":" . $block->getFloorZ() . ":" . $block->getLevel()->getName();
            if (isset($this->plugin->chests[$string])) {
                unset($this->plugin->chests[$string]);
            }
        }
    }
}
