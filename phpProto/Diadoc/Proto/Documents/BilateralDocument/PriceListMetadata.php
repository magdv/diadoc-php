<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Documents/BilateralDocument.proto

namespace Diadoc\Proto\Documents\BilateralDocument;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Documents.BilateralDocument.PriceListMetadata</code>
 */
class PriceListMetadata extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Documents.BilateralDocument.BilateralDocumentStatus DocumentStatus = 1;</code>
     */
    private $DocumentStatus = 0;
    /**
     * Generated from protobuf field <code>string PriceListEffectiveDate = 2;</code>
     */
    private $PriceListEffectiveDate = '';
    /**
     * Generated from protobuf field <code>string ContractDocumentDate = 3;</code>
     */
    private $ContractDocumentDate = '';
    /**
     * Generated from protobuf field <code>string ContractDocumentNumber = 4;</code>
     */
    private $ContractDocumentNumber = '';
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Documents.ReceiptStatus ReceiptStatus = 5;</code>
     */
    private $ReceiptStatus = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $DocumentStatus
     *     @type string $PriceListEffectiveDate
     *     @type string $ContractDocumentDate
     *     @type string $ContractDocumentNumber
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
     * Generated from protobuf field <code>string PriceListEffectiveDate = 2;</code>
     * @return string
     */
    public function getPriceListEffectiveDate()
    {
        return $this->PriceListEffectiveDate;
    }

    /**
     * Generated from protobuf field <code>string PriceListEffectiveDate = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setPriceListEffectiveDate($var)
    {
        GPBUtil::checkString($var, True);
        $this->PriceListEffectiveDate = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string ContractDocumentDate = 3;</code>
     * @return string
     */
    public function getContractDocumentDate()
    {
        return $this->ContractDocumentDate;
    }

    /**
     * Generated from protobuf field <code>string ContractDocumentDate = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setContractDocumentDate($var)
    {
        GPBUtil::checkString($var, True);
        $this->ContractDocumentDate = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string ContractDocumentNumber = 4;</code>
     * @return string
     */
    public function getContractDocumentNumber()
    {
        return $this->ContractDocumentNumber;
    }

    /**
     * Generated from protobuf field <code>string ContractDocumentNumber = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setContractDocumentNumber($var)
    {
        GPBUtil::checkString($var, True);
        $this->ContractDocumentNumber = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Documents.ReceiptStatus ReceiptStatus = 5;</code>
     * @return int
     */
    public function getReceiptStatus()
    {
        return $this->ReceiptStatus;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Documents.ReceiptStatus ReceiptStatus = 5;</code>
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

