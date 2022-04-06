<?php

namespace srag\DIC\SrLpReport\Plugin;

/**
 * Interface Pluginable
 *
 * @package srag\DIC\SrLpReport\Plugin
 */
interface Pluginable
{

    /**
     * @return PluginInterface
     */
    public function getPlugin() : PluginInterface;


    /**
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function withPlugin(PluginInterface $plugin)/*: static*/ ;
}
