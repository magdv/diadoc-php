<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Documents/BilateralDocument.proto

namespace Diadoc\Proto\Documents\BilateralDocument;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Documents.BilateralDocument.SupplementaryAgreementMetadata</code>
 */
class SupplementaryAgreementMetadata extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Documents.BilateralDocument.BilateralDocumentStatus DocumentStatus = 1;</code>
     */
    private $DocumentStatus = 0;
    /**
     * Generated from protobuf field <code>string Total = 2;</code>
     */
    private $Total = '';
    /**
     * Generated from protobuf field <code>string ContractType = 3;</code>
     */
    private $ContractType = '';
    /**
     * Generated from protobuf field <code>string ContractNumber = 4;</code>
     */
    private $ContractNumber = '';
    /**
     * Generated from protobuf field <code>string ContractDate = 5;</code>
     */
    private $ContractDate = '';
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Documents.ReceiptStatus ReceiptStatus = 6;</code>
     */
    private $ReceiptStatus = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $DocumentStatus
     *     @type string $Total
     *     @type string $ContractType
     *     @type string $ContractNumber
     *     @type string $ContractDate
     *     @type int $ReceiptStatus
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Documents\BilateralDocument::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Documents.BilateralDocument.BilateralDocumentStatus DocumentStatus = 1;</code>
     * @return int
     */
    public function getDocumentStatus()
    {
        return $this->DocumentStatus;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Documents.BilateralDocument.BilateralDocumentStatus DocumentStatus = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setDocumentStatus($var)
    {
        GPBUtil::checkEnum($var, \Diadoc\Proto\Documents\BilateralDocument\BilateralDocumentStatus::class);
        $this->DocumentStatus = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string Total = 2;</code>
     * @return string
     */
    public function getTotal()
    {
        return $this->Total;
    }

    /**
     * Generated from protobuf field <code>string Total = 2;</code>
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
     * Generated from protobuf field <code>string ContractType = 3;</code>
     * @return string
     */
    public function getContractType()
    {
        return $this->ContractType;
    }

    /**
     * Generated from protobuf field <code>string ContractType = 3;</code>
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
     * Generated from protobuf field <code>string ContractNumber = 4;</code>
     * @return string
     */
    public function getContractNumber()
    {
        return $this->ContractNumber;
    }

    /**
     * Generated from protobuf field <code>string ContractNumber = 4;</code>
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
     * Generated from protobuf field <code>string ContractDate = 5;</code>
     * @return string
     */
    public function getContractDate()
    {
        return $this->ContractDate;
    }

    /**
     * Generated from protobuf field <code>string ContractDate = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setContractDate($var)
    {
        GPBUtil::checkString($var, True);
        $this->ContractDate = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Documents.ReceiptStatus ReceiptStatus = 6;</code>
     * @return int
     */
    public function getReceiptStatus()
    {
        return $this->ReceiptStatus;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Documents.ReceiptStatus ReceiptStatus = 6;</code>
     * @param int $var
     * @return $this
     */
    public function setReceiptStatus($var)
    {
        GPBUtil::checkEnum($var, \Diadoc\Proto\Documents\ReceiptStatus::class);
        $this->ReceiptStatus = $var;

        return $this;
    }

}

