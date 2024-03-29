<?php

namespace srag\CommentsUI\SrLpReport\Comment;

use ilDateTime;
use srag\DIC\SrLpReport\DICTrait;
use stdClass;

/**
 * Class Factory
 *
 * @package srag\CommentsUI\SrLpReport\Comment
 */
final class Factory implements FactoryInterface
{

    use DICTrait;

    /**
     * @var FactoryInterface|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return FactoryInterface
     */
    public static function getInstance() : FactoryInterface
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @inheritDoc
     */
    public function fromDB(stdClass $data) : Comment
    {
        $comment = $this->newInstance();

        $comment->setId($data->id);
        $comment->setComment($data->comment);
        $comment->setReportObjId($data->report_obj_id);
        $comment->setReportUserId($data->report_user_id);
        $comment->setCreatedTimestamp((new ilDateTime($data->created_timestamp, IL_CAL_DATETIME))->getUnixTime());
        $comment->setCreatedUserId($data->created_user_id);
        $comment->setUpdatedTimestamp((new ilDateTime($data->updated_timestamp, IL_CAL_DATETIME))->getUnixTime());
        $comment->setUpdatedUserId($data->updated_user_id);
        $comment->setIsShared($data->is_shared);
        $comment->setDeleted($data->deleted);

        return $comment;
    }


    /**
     * @inheritDoc
     */
    public function newInstance() : Comment
    {
        $comment = new CommentAR();

        return $comment;
    }
}
