<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Forwarding/ForwardingApi.proto

namespace Diadoc\Proto\Forwarding;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Forwarding.GetForwardedDocumentsRequest</code>
 */
class GetForwardedDocumentsRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Forwarding.ForwardedDocumentId ForwardedDocumentIds = 1;</code>
     */
    private $ForwardedDocumentIds;
    /**
     * Generated from protobuf field <code>bool InjectEntityContent = 2;</code>
     */
    private $InjectEntityContent = false;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Diadoc\Proto\Forwarding\ForwardedDocumentId[]|\Google\Protobuf\Internal\RepeatedField $ForwardedDocumentIds
     *     @type bool $InjectEntityContent
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Forwarding\ForwardingApi::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Forwarding.ForwardedDocumentId ForwardedDocumentIds = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getForwardedDocumentIds()
    {
        return $this->ForwardedDocumentIds;
    }

    /**
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Forwarding.ForwardedDocumentId ForwardedDocumentIds = 1;</code>
     * @param \Diadoc\Proto\Forwarding\ForwardedDocumentId[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setForwardedDocumentIds($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Diadoc\Proto\Forwarding\ForwardedDocumentId::class);
        $this->ForwardedDocumentIds = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool InjectEntityContent = 2;</code>
     * @return bool
     */
    public function getInjectEntityContent()
    {
        return $this->InjectEntityContent;
    }

    /**
     * Generated from protobuf field <code>bool InjectEntityContent = 2;</code>
     * @param bool $var
     * @return $this
     */
    public function setInjectEntityContent($var)
    {
        GPBUtil::checkBool($var);
        $this->InjectEntityContent = $var;

        return $this;
    }

}

