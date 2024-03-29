<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Docflow/DocumentInfo.proto

namespace Diadoc\Proto\Docflow;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Docflow.SupplementaryAgreementDocumentInfo</code>
 */
class SupplementaryAgreementDocumentInfo extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string Total = 1;</code>
     */
    private $Total = '';
    /**
     * Generated from protobuf field <code>string ContractType = 2;</code>
     */
    private $ContractType = '';
    /**
     * Generated from protobuf field <code>string ContractNumber = 3;</code>
     */
    private $ContractNumber = '';
    /**
     * Generated from protobuf field <code>string ContractDate = 4;</code>
     */
    private $ContractDate = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $Total
     *     @type string $ContractType
     *     @type string $ContractNumber
     *     @type string $ContractDate
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Docflow\DocumentInfo::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string Total = 1;</code>
     * @return string
     */
    public function getTotal()
    {
        return $this->Total;
    }

    /**
     * Generated from protobuf field <code>string Total = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setTotal($var)
    {
        GPBUtil::checkString($var, True);
        $this->Total = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string ContractType = 2;</code>
     * @return string
     */
    public function getContractType()
    {
        return $this->ContractType;
    }

    /**
     * Generated from protobuf field <code>string ContractType = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setContractType($var)
    {
        GPBUtil::checkString($var, True);
        $this->ContractType = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string ContractNumber = 3;</code>
     * @return string
     */
    public function getContractNumber()
    {
        return $this->ContractNumber;
    }

    /**
     * Generated from protobuf field <code>string ContractNumber = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setContractNumber($var)
    {
        GPBUtil::checkString($var, True);
        $this->ContractNumber = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string ContractDate = 4;</code>
     * @return string
     */
    public function getContractDate()
    {
        return $this->ContractDate;
    }

    /**
     * Generated from protobuf field <code>string ContractDate = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setContractDate($var)
    {
        GPBUtil::checkString($var, True);
        $this->ContractDate = $var;

        return $this;
    }

}

