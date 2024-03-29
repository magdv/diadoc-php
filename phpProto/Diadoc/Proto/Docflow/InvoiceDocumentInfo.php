<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Docflow/DocumentInfo.proto

namespace Diadoc\Proto\Docflow;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Docflow.InvoiceDocumentInfo</code>
 */
class InvoiceDocumentInfo extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string Total = 1;</code>
     */
    private $Total = '';
    /**
     * Generated from protobuf field <code>string Vat = 2;</code>
     */
    private $Vat = '';
    /**
     * Generated from protobuf field <code>int32 CurrencyCode = 3;</code>
     */
    private $CurrencyCode = 0;
    /**
     * for InvoiceRevision
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.DocumentDateAndNumber OriginalInvoiceDateAndNumber = 4;</code>
     */
    private $OriginalInvoiceDateAndNumber = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $Total
     *     @type string $Vat
     *     @type int $CurrencyCode
     *     @type \Diadoc\Proto\Docflow\DocumentDateAndNumber $OriginalInvoiceDateAndNumber
     *           for InvoiceRevision
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
     * Generated from protobuf field <code>string Vat = 2;</code>
     * @return string
     */
    public function getVat()
    {
        return $this->Vat;
    }

    /**
     * Generated from protobuf field <code>string Vat = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setVat($var)
    {
        GPBUtil::checkString($var, True);
        $this->Vat = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 CurrencyCode = 3;</code>
     * @return int
     */
    public function getCurrencyCode()
    {
        return $this->CurrencyCode;
    }

    /**
     * Generated from protobuf field <code>int32 CurrencyCode = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setCurrencyCode($var)
    {
        GPBUtil::checkInt32($var);
        $this->CurrencyCode = $var;

        return $this;
    }

    /**
     * for InvoiceRevision
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.DocumentDateAndNumber OriginalInvoiceDateAndNumber = 4;</code>
     * @return \Diadoc\Proto\Docflow\DocumentDateAndNumber
     */
    public function getOriginalInvoiceDateAndNumber()
    {
        return $this->OriginalInvoiceDateAndNumber;
    }

    /**
     * for InvoiceRevision
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.DocumentDateAndNumber OriginalInvoiceDateAndNumber = 4;</code>
     * @param \Diadoc\Proto\Docflow\DocumentDateAndNumber $var
     * @return $this
     */
    public function setOriginalInvoiceDateAndNumber($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Docflow\DocumentDateAndNumber::class);
        $this->OriginalInvoiceDateAndNumber = $var;

        return $this;
    }

}

