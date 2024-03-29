<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Events/DiadocMessage-GetApi.proto

namespace Diadoc\Proto\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Events.BoxEvent</code>
 */
class BoxEvent extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string EventId = 1;</code>
     */
    private $EventId = '';
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Events.Message Message = 2;</code>
     */
    private $Message = null;
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Events.MessagePatch Patch = 3;</code>
     */
    private $Patch = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $EventId
     *     @type \Diadoc\Proto\Events\Message $Message
     *     @type \Diadoc\Proto\Events\MessagePatch $Patch
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events\DiadocMessageGetApi::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string EventId = 1;</code>
     * @return string
     */
    public function getEventId()
    {
        return $this->EventId;
    }

    /**
     * Generated from protobuf field <code>string EventId = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setEventId($var)
    {
        GPBUtil::checkString($var, True);
        $this->EventId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Events.Message Message = 2;</code>
     * @return \Diadoc\Proto\Events\Message
     */
    public function getMessage()
    {
        return $this->Message;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Events.Message Message = 2;</code>
     * @param \Diadoc\Proto\Events\Message $var
     * @return $this
     */
    public function setMessage($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Events\Message::class);
        $this->Message = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Events.MessagePatch Patch = 3;</code>
     * @return \Diadoc\Proto\Events\MessagePatch
     */
    public function getPatch()
    {
        return $this->Patch;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Events.MessagePatch Patch = 3;</code>
     * @param \Diadoc\Proto\Events\MessagePatch $var
     * @return $this
     */
    public function setPatch($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Events\MessagePatch::class);
        $this->Patch = $var;

        return $this;
    }

}

