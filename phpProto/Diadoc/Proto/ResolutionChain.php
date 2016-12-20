<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: ResolutionChainList.proto

namespace Diadoc\Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.ResolutionChain</code>
 */
class ResolutionChain extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string ChainId = 1;</code>
     */
    private $ChainId = '';
    /**
     * Generated from protobuf field <code>string Name = 2;</code>
     */
    private $Name = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $ChainId
     *     @type string $Name
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\ResolutionChainList::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string ChainId = 1;</code>
     * @return string
     */
    public function getChainId()
    {
        return $this->ChainId;
    }

    /**
     * Generated from protobuf field <code>string ChainId = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setChainId($var)
    {
        GPBUtil::checkString($var, True);
        $this->ChainId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string Name = 2;</code>
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Generated from protobuf field <code>string Name = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkString($var, True);
        $this->Name = $var;

        return $this;
    }

}

