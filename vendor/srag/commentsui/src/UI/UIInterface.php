<?php

namespace srag\CommentsUI\SrLpReport\UI;

use srag\CommentsUI\SrLpReport\Ctrl\CtrlInterface;
use srag\DIC\SrLpReport\Plugin\Pluginable;

/**
 * Interface UIInterface
 *
 * @package srag\CommentsUI\SrLpReport\UI
 */
interface UIInterface extends Pluginable
{

    const LANG_MODULE_COMMENTSUI = "commentsui";


    /**
     * @return string
     */
    public function render() : string;


    /**
     * @param CtrlInterface $ctrl_class
     *
     * @return self
     */
    public function withCtrlClass(CtrlInterface $ctrl_class) : self;


    /**
     * @param string $id
     *
     * @return self
     */
    public function withId(string $id) : self;
}
