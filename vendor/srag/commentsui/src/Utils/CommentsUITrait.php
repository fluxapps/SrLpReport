<?php

namespace srag\CommentsUI\SrLpReport\Utils;

use srag\CommentsUI\SrLpReport\Comment\Repository;
use srag\CommentsUI\SrLpReport\Comment\RepositoryInterface;
use srag\CommentsUI\SrLpReport\UI\UI;
use srag\CommentsUI\SrLpReport\UI\UIInterface;

/**
 * Trait CommentsUITrait
 *
 * @package srag\CommentsUI\SrLpReport\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait CommentsUITrait
{

    /**
     * @return RepositoryInterface
     */
    protected static function comments() : RepositoryInterface
    {
        return Repository::getInstance();
    }


    /**
     * @return UIInterface
     */
    protected static function commentsUI() : UIInterface
    {
        return new UI();
    }
}
