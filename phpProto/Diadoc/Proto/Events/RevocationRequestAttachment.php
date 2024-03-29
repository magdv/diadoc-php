<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Events/DiadocMessage-PostApi.proto

namespace Diadoc\Proto\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Events.RevocationRequestAttachment</code>
 */
class RevocationRequestAttachment extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string ParentEntityId = 1;</code>
     */
    private $ParentEntityId = '';
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Events.SignedContent SignedContent = 2;</code>
     */
    private $SignedContent = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $ParentEntityId
     *     @type \Diadoc\Proto\Events\SignedContent $SignedContent
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events\DiadocMessagePostApi::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string ParentEntityId = 1;</code>
     * @return string
     */
    public function getParentEntityId()
    {
        return $this->ParentEntityId;
    }

    /**
     * Generated from protobuf field <code>string ParentEntityId = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setParentEntityId($var)
    {
        GPBUtil::checkString($var, True);
        $this->ParentEntityId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Events.SignedContent SignedContent = 2;</code>
     * @return \Diadoc\Proto\Events\SignedContent
     */
    public function getSignedContent()
    {
        return $this->SignedContent;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Events.SignedContent SignedContent = 2;</code>
     * @param \Diadoc\Proto\Events\SignedContent $var
     * @return $this
     */
    public function setSignedContent($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Events\SignedContent::class);
        $this->SignedContent = $var;

        return $this;
    }

}

