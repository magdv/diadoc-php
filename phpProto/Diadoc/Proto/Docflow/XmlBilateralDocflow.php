<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Docflow/XmlBilateralDocflow.proto

namespace Diadoc\Proto\Docflow;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Docflow.XmlBilateralDocflow</code>
 */
class XmlBilateralDocflow extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bool IsFinished = 1;</code>
     */
    private $IsFinished = false;
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.ReceiptDocflow ReceiptDocflow = 2;</code>
     */
    private $ReceiptDocflow = null;
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.BuyerTitleDocflow BuyerTitleDocflow = 3;</code>
     */
    private $BuyerTitleDocflow = null;
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.RecipientSignatureRejectionDocflow RecipientSignatureRejectionDocflow = 4;</code>
     */
    private $RecipientSignatureRejectionDocflow = null;
    /**
     * Generated from protobuf field <code>bool IsReceiptRequested = 5;</code>
     */
    private $IsReceiptRequested = false;
    /**
     * Generated from protobuf field <code>bool IsDocumentSignedByRecipient = 6;</code>
     */
    private $IsDocumentSignedByRecipient = false;
    /**
     * Generated from protobuf field <code>bool IsDocumentRejectedByRecipient = 7;</code>
     */
    private $IsDocumentRejectedByRecipient = false;
    /**
     * Generated from protobuf field <code>bool CanDocumentBeReceipted = 8;</code>
     */
    private $CanDocumentBeReceipted = false;
    /**
     * Generated from protobuf field <code>bool CanDocumentBeSignedBySender = 9;</code>
     */
    private $CanDocumentBeSignedBySender = false;
    /**
     * Generated from protobuf field <code>bool CanDocumentBeSignedOrRejectedByRecipient = 10;</code>
     */
    private $CanDocumentBeSignedOrRejectedByRecipient = false;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type bool $IsFinished
     *     @type \Diadoc\Proto\Docflow\ReceiptDocflow $ReceiptDocflow
     *     @type \Diadoc\Proto\Docflow\BuyerTitleDocflow $BuyerTitleDocflow
     *     @type \Diadoc\Proto\Docflow\RecipientSignatureRejectionDocflow $RecipientSignatureRejectionDocflow
     *     @type bool $IsReceiptRequested
     *     @type bool $IsDocumentSignedByRecipient
     *     @type bool $IsDocumentRejectedByRecipient
     *     @type bool $CanDocumentBeReceipted
     *     @type bool $CanDocumentBeSignedBySender
     *     @type bool $CanDocumentBeSignedOrRejectedByRecipient
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Docflow\XmlBilateralDocflow::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bool IsFinished = 1;</code>
     * @return bool
     */
    public function getIsFinished()
    {
        return $this->IsFinished;
    }

    /**
     * Generated from protobuf field <code>bool IsFinished = 1;</code>
     * @param bool $var
     * @return $this
     */
    public function setIsFinished($var)
    {
        GPBUtil::checkBool($var);
        $this->IsFinished = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.ReceiptDocflow ReceiptDocflow = 2;</code>
     * @return \Diadoc\Proto\Docflow\ReceiptDocflow
     */
    public function getReceiptDocflow()
    {
        return $this->ReceiptDocflow;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.ReceiptDocflow ReceiptDocflow = 2;</code>
     * @param \Diadoc\Proto\Docflow\ReceiptDocflow $var
     * @return $this
     */
    public function setReceiptDocflow($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Docflow\ReceiptDocflow::class);
        $this->ReceiptDocflow = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.BuyerTitleDocflow BuyerTitleDocflow = 3;</code>
     * @return \Diadoc\Proto\Docflow\BuyerTitleDocflow
     */
    public function getBuyerTitleDocflow()
    {
        return $this->BuyerTitleDocflow;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.BuyerTitleDocflow BuyerTitleDocflow = 3;</code>
     * @param \Diadoc\Proto\Docflow\BuyerTitleDocflow $var
     * @return $this
     */
    public function setBuyerTitleDocflow($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Docflow\BuyerTitleDocflow::class);
        $this->BuyerTitleDocflow = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.RecipientSignatureRejectionDocflow RecipientSignatureRejectionDocflow = 4;</code>
     * @return \Diadoc\Proto\Docflow\RecipientSignatureRejectionDocflow
     */
    public function getRecipientSignatureRejectionDocflow()
    {
        return $this->RecipientSignatureRejectionDocflow;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.RecipientSignatureRejectionDocflow RecipientSignatureRejectionDocflow = 4;</code>
     * @param \Diadoc\Proto\Docflow\RecipientSignatureRejectionDocflow $var
     * @return $this
     */
    public function setRecipientSignatureRejectionDocflow($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Docflow\RecipientSignatureRejectionDocflow::class);
        $this->RecipientSignatureRejectionDocflow = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool IsReceiptRequested = 5;</code>
     * @return bool
     */
    public function getIsReceiptRequested()
    {
        return $this->IsReceiptRequested;
    }

    /**
     * Generated from protobuf field <code>bool IsReceiptRequested = 5;</code>
     * @param bool $var
     * @return $this
     */
    public function setIsReceiptRequested($var)
    {
        GPBUtil::checkBool($var);
        $this->IsReceiptRequested = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool IsDocumentSignedByRecipient = 6;</code>
     * @return bool
     */
    public function getIsDocumentSignedByRecipient()
    {
        return $this->IsDocumentSignedByRecipient;
    }

    /**
     * Generated from protobuf field <code>bool IsDocumentSignedByRecipient = 6;</code>
     * @param bool $var
     * @return $this
     */
    public function setIsDocumentSignedByRecipient($var)
    {
        GPBUtil::checkBool($var);
        $this->IsDocumentSignedByRecipient = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool IsDocumentRejectedByRecipient = 7;</code>
     * @return bool
     */
    public function getIsDocumentRejectedByRecipient()
    {
        return $this->IsDocumentRejectedByRecipient;
    }

    /**
     * Generated from protobuf field <code>bool IsDocumentRejectedByRecipient = 7;</code>
     * @param bool $var
     * @return $this
     */
    public function setIsDocumentRejectedByRecipient($var)
    {
        GPBUtil::checkBool($var);
        $this->IsDocumentRejectedByRecipient = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool CanDocumentBeReceipted = 8;</code>
     * @return bool
     */
    public function getCanDocumentBeReceipted()
    {
        return $this->CanDocumentBeReceipted;
    }

    /**
     * Generated from protobuf field <code>bool CanDocumentBeReceipted = 8;</code>
     * @param bool $var
     * @return $this
     */
    public function setCanDocumentBeReceipted($var)
    {
        GPBUtil::checkBool($var);
        $this->CanDocumentBeReceipted = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool CanDocumentBeSignedBySender = 9;</code>
     * @return bool
     */
    public function getCanDocumentBeSignedBySender()
    {
        return $this->CanDocumentBeSignedBySender;
    }

    /**
     * Generated from protobuf field <code>bool CanDocumentBeSignedBySender = 9;</code>
     * @param bool $var
     * @return $this
     */
    public function setCanDocumentBeSignedBySender($var)
    {
        GPBUtil::checkBool($var);
        $this->CanDocumentBeSignedBySender = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool CanDocumentBeSignedOrRejectedByRecipient = 10;</code>
     * @return bool
     */
    public function getCanDocumentBeSignedOrRejectedByRecipient()
    {
        return $this->CanDocumentBeSignedOrRejectedByRecipient;
    }

    /**
     * Generated from protobuf field <code>bool CanDocumentBeSignedOrRejectedByRecipient = 10;</code>
     * @param bool $var
     * @return $this
     */
    public function setCanDocumentBeSignedOrRejectedByRecipient($var)
    {
        GPBUtil::checkBool($var);
        $this->CanDocumentBeSignedOrRejectedByRecipient = $var;

        return $this;
    }

}

