<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Counteragent.proto

namespace Diadoc\Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.CounteragentCertificateList</code>
 */
class CounteragentCertificateList extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Certificate Certificates = 1;</code>
     */
    private $Certificates;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Diadoc\Proto\Certificate[]|\Google\Protobuf\Internal\RepeatedField $Certificates
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Counteragent::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Certificate Certificates = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getCertificates()
    {
        return $this->Certificates;
    }

    /**
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Certificate Certificates = 1;</code>
     * @param \Diadoc\Proto\Certificate[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setCertificates($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Diadoc\Proto\Certificate::class);
        $this->Certificates = $arr;

        return $this;
    }

}

