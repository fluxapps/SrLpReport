<?php

namespace srag\CommentsUI\SrLpReport\Comment;

use stdClass;

/**
 * Interface FactoryInterface
 *
 * @package srag\CommentsUI\SrLpReport\Comment
 */
interface FactoryInterface
{

    /**
     * @param stdClass $data
     *
     * @return Comment
     */
    public function fromDB(stdClass $data) : Comment;


    /**
     * @return Comment
     */
    public function newInstance() : Comment;
}
