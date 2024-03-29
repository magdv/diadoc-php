<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Docflow/DocflowApi.proto

namespace Diadoc\Proto\Docflow;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Docflow.GetDocflowsByPacketIdResponse</code>
 */
class GetDocflowsByPacketIdResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Docflow.FetchedDocument Documents = 1;</code>
     */
    private $Documents;
    /**
     * Generated from protobuf field <code>bytes NextPageIndexKey = 2;</code>
     */
    private $NextPageIndexKey = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Diadoc\Proto\Docflow\FetchedDocument[]|\Google\Protobuf\Internal\RepeatedField $Documents
     *     @type string $NextPageIndexKey
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Docflow\DocflowApi::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Docflow.FetchedDocument Documents = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getDocuments()
    {
        return $this->Documents;
    }

    /**
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Docflow.FetchedDocument Documents = 1;</code>
     * @param \Diadoc\Proto\Docflow\FetchedDocument[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setDocuments($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Diadoc\Proto\Docflow\FetchedDocument::class);
        $this->Documents = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes NextPageIndexKey = 2;</code>
     * @return string
     */
    public function getNextPageIndexKey()
    {
        return $this->NextPageIndexKey;
    }

    /**
     * Generated from protobuf field <code>bytes NextPageIndexKey = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setNextPageIndexKey($var)
    {
        GPBUtil::checkString($var, False);
        $this->NextPageIndexKey = $var;

        return $this;
    }

}

