<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Forwarding/ForwardedDocument.proto

namespace Diadoc\Proto\Forwarding;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Forwarding.ForwardedDocument</code>
 */
class ForwardedDocument extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Timestamp ForwardTimestamp = 1;</code>
     */
    private $ForwardTimestamp = null;
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Forwarding.ForwardedDocumentId ForwardedDocumentId = 2;</code>
     */
    private $ForwardedDocumentId = null;
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.DocumentWithDocflow DocumentWithDocflow = 3;</code>
     */
    private $DocumentWithDocflow = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Diadoc\Proto\Timestamp $ForwardTimestamp
     *     @type \Diadoc\Proto\Forwarding\ForwardedDocumentId $ForwardedDocumentId
     *     @type \Diadoc\Proto\Docflow\DocumentWithDocflow $DocumentWithDocflow
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Forwarding\ForwardedDocument::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Timestamp ForwardTimestamp = 1;</code>
     * @return \Diadoc\Proto\Timestamp
     */
    public function getForwardTimestamp()
    {
        return $this->ForwardTimestamp;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Timestamp ForwardTimestamp = 1;</code>
     * @param \Diadoc\Proto\Timestamp $var
     * @return $this
     */
    public function setForwardTimestamp($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Timestamp::class);
        $this->ForwardTimestamp = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Forwarding.ForwardedDocumentId ForwardedDocumentId = 2;</code>
     * @return \Diadoc\Proto\Forwarding\ForwardedDocumentId
     */
    public function getForwardedDocumentId()
    {
        return $this->ForwardedDocumentId;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Forwarding.ForwardedDocumentId ForwardedDocumentId = 2;</code>
     * @param \Diadoc\Proto\Forwarding\ForwardedDocumentId $var
     * @return $this
     */
    public function setForwardedDocumentId($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Forwarding\ForwardedDocumentId::class);
        $this->ForwardedDocumentId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.DocumentWithDocflow DocumentWithDocflow = 3;</code>
     * @return \Diadoc\Proto\Docflow\DocumentWithDocflow
     */
    public function getDocumentWithDocflow()
    {
        return $this->DocumentWithDocflow;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.DocumentWithDocflow DocumentWithDocflow = 3;</code>
     * @param \Diadoc\Proto\Docflow\DocumentWithDocflow $var
     * @return $this
     */
    public function setDocumentWithDocflow($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Docflow\DocumentWithDocflow::class);
        $this->DocumentWithDocflow = $var;

        return $this;
    }

}

