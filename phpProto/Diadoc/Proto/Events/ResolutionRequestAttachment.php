<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Events/DiadocMessage-PostApi.proto

namespace Diadoc\Proto\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Events.ResolutionRequestAttachment</code>
 */
class ResolutionRequestAttachment extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string InitialDocumentId = 1;</code>
     */
    private $InitialDocumentId = '';
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Events.ResolutionRequestType Type = 2;</code>
     */
    private $Type = 0;
    /**
     * Generated from protobuf field <code>string TargetUserId = 3;</code>
     */
    private $TargetUserId = '';
    /**
     * Generated from protobuf field <code>string TargetDepartmentId = 4;</code>
     */
    private $TargetDepartmentId = '';
    /**
     * Generated from protobuf field <code>string Comment = 5;</code>
     */
    private $Comment = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $InitialDocumentId
     *     @type int $Type
     *     @type string $TargetUserId
     *     @type string $TargetDepartmentId
     *     @type string $Comment
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events\DiadocMessagePostApi::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string InitialDocumentId = 1;</code>
     * @return string
     */
    public function getInitialDocumentId()
    {
        return $this->InitialDocumentId;
    }

    /**
     * Generated from protobuf field <code>string InitialDocumentId = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setInitialDocumentId($var)
    {
        GPBUtil::checkString($var, True);
        $this->InitialDocumentId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Events.ResolutionRequestType Type = 2;</code>
     * @return int
     */
    public function getType()
    {
        return $this->Type;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Events.ResolutionRequestType Type = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setType($var)
    {
        GPBUtil::checkEnum($var, \Diadoc\Proto\Events\ResolutionRequestType::class);
        $this->Type = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string TargetUserId = 3;</code>
     * @return string
     */
    public function getTargetUserId()
    {
        return $this->TargetUserId;
    }

    /**
     * Generated from protobuf field <code>string TargetUserId = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setTargetUserId($var)
    {
        GPBUtil::checkString($var, True);
        $this->TargetUserId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string TargetDepartmentId = 4;</code>
     * @return string
     */
    public function getTargetDepartmentId()
    {
        return $this->TargetDepartmentId;
    }

    /**
     * Generated from protobuf field <code>string TargetDepartmentId = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setTargetDepartmentId($var)
    {
        GPBUtil::checkString($var, True);
        $this->TargetDepartmentId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string Comment = 5;</code>
     * @return string
     */
    public function getComment()
    {
        return $this->Comment;
    }

    /**
     * Generated from protobuf field <code>string Comment = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setComment($var)
    {
        GPBUtil::checkString($var, True);
        $this->Comment = $var;

        return $this;
    }

}

