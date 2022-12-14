<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: ResolutionChainList.proto

namespace Diadoc\Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.ResolutionChainList</code>
 */
class ResolutionChainList extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated .Diadoc.Proto.ResolutionChain ResolutionChains = 1;</code>
     */
    private $ResolutionChains;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Diadoc\Proto\ResolutionChain[]|\Google\Protobuf\Internal\RepeatedField $ResolutionChains
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\ResolutionChainList::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .Diadoc.Proto.ResolutionChain ResolutionChains = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getResolutionChains()
    {
        return $this->ResolutionChains;
    }

    /**
     * Generated from protobuf field <code>repeated .Diadoc.Proto.ResolutionChain ResolutionChains = 1;</code>
     * @param \Diadoc\Proto\ResolutionChain[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setResolutionChains($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Diadoc\Proto\ResolutionChain::class);
        $this->ResolutionChains = $arr;

        return $this;
    }

}

