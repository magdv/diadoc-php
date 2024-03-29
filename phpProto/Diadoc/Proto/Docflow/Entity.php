<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Docflow/Attachment.proto

namespace Diadoc\Proto\Docflow;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Docflow.Entity</code>
 */
class Entity extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string EntityId = 1;</code>
     */
    private $EntityId = '';
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Timestamp CreationTimestamp = 2;</code>
     */
    private $CreationTimestamp = null;
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Content Content = 3;</code>
     */
    private $Content = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $EntityId
     *     @type \Diadoc\Proto\Timestamp $CreationTimestamp
     *     @type \Diadoc\Proto\Content $Content
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Docflow\Attachment::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string EntityId = 1;</code>
     * @return string
     */
    public function getEntityId()
    {
        return $this->EntityId;
    }

    /**
     * Generated from protobuf field <code>string EntityId = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setEntityId($var)
    {
        GPBUtil::checkString($var, True);
        $this->EntityId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Timestamp CreationTimestamp = 2;</code>
     * @return \Diadoc\Proto\Timestamp
     */
    public function getCreationTimestamp()
    {
        return $this->CreationTimestamp;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Timestamp CreationTimestamp = 2;</code>
     * @param \Diadoc\Proto\Timestamp $var
     * @return $this
     */
    public function setCreationTimestamp($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Timestamp::class);
        $this->CreationTimestamp = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Content Content = 3;</code>
     * @return \Diadoc\Proto\Content
     */
    public function getContent()
    {
        return $this->Content;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Content Content = 3;</code>
     * @param \Diadoc\Proto\Content $var
     * @return $this
     */
    public function setContent($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Content::class);
        $this->Content = $var;

        return $this;
    }

}

