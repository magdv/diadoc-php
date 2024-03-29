<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Invoicing/Official.proto

namespace Diadoc\Proto\Invoicing;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Invoicing.Attorney</code>
 */
class Attorney extends \Google\Protobuf\Internal\Message
{
    /**
     * дата выдачи доверенности
     *
     * Generated from protobuf field <code>string Date = 1;</code>
     */
    private $Date = '';
    /**
     * номер доверенности
     *
     * Generated from protobuf field <code>string Number = 2;</code>
     */
    private $Number = '';
    /**
     * организация, представитель которой выдал доверенность
     *
     * Generated from protobuf field <code>string IssuerOrganizationName = 3;</code>
     */
    private $IssuerOrganizationName = '';
    /**
     * лицо, выдавшее доверенность
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.Official IssuerPerson = 4;</code>
     */
    private $IssuerPerson = null;
    /**
     * дополнительная информация о выдавшем доверенность
     *
     * Generated from protobuf field <code>string IssuerAdditionalInfo = 5;</code>
     */
    private $IssuerAdditionalInfo = '';
    /**
     * лицо, получившее доверенность
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.Official RecipientPerson = 6;</code>
     */
    private $RecipientPerson = null;
    /**
     * дополнительная информация о получившем доверенность
     *
     * Generated from protobuf field <code>string RecipientAdditionalInfo = 7;</code>
     */
    private $RecipientAdditionalInfo = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $Date
     *           дата выдачи доверенности
     *     @type string $Number
     *           номер доверенности
     *     @type string $IssuerOrganizationName
     *           организация, представитель которой выдал доверенность
     *     @type \Diadoc\Proto\Invoicing\Official $IssuerPerson
     *           лицо, выдавшее доверенность
     *     @type string $IssuerAdditionalInfo
     *           дополнительная информация о выдавшем доверенность
     *     @type \Diadoc\Proto\Invoicing\Official $RecipientPerson
     *           лицо, получившее доверенность
     *     @type string $RecipientAdditionalInfo
     *           дополнительная информация о получившем доверенность
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Invoicing\Official::initOnce();
        parent::__construct($data);
    }

    /**
     * дата выдачи доверенности
     *
     * Generated from protobuf field <code>string Date = 1;</code>
     * @return string
     */
    public function getDate()
    {
        return $this->Date;
    }

    /**
     * дата выдачи доверенности
     *
     * Generated from protobuf field <code>string Date = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setDate($var)
    {
        GPBUtil::checkString($var, True);
        $this->Date = $var;

        return $this;
    }

    /**
     * номер доверенности
     *
     * Generated from protobuf field <code>string Number = 2;</code>
     * @return string
     */
    public function getNumber()
    {
        return $this->Number;
    }

    /**
     * номер доверенности
     *
     * Generated from protobuf field <code>string Number = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setNumber($var)
    {
        GPBUtil::checkString($var, True);
        $this->Number = $var;

        return $this;
    }

    /**
     * организация, представитель которой выдал доверенность
     *
     * Generated from protobuf field <code>string IssuerOrganizationName = 3;</code>
     * @return string
     */
    public function getIssuerOrganizationName()
    {
        return $this->IssuerOrganizationName;
    }

    /**
     * организация, представитель которой выдал доверенность
     *
     * Generated from protobuf field <code>string IssuerOrganizationName = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setIssuerOrganizationName($var)
    {
        GPBUtil::checkString($var, True);
        $this->IssuerOrganizationName = $var;

        return $this;
    }

    /**
     * лицо, выдавшее доверенность
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.Official IssuerPerson = 4;</code>
     * @return \Diadoc\Proto\Invoicing\Official
     */
    public function getIssuerPerson()
    {
        return $this->IssuerPerson;
    }

    /**
     * лицо, выдавшее доверенность
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.Official IssuerPerson = 4;</code>
     * @param \Diadoc\Proto\Invoicing\Official $var
     * @return $this
     */
    public function setIssuerPerson($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Invoicing\Official::class);
        $this->IssuerPerson = $var;

        return $this;
    }

    /**
     * дополнительная информация о выдавшем доверенность
     *
     * Generated from protobuf field <code>string IssuerAdditionalInfo = 5;</code>
     * @return string
     */
    public function getIssuerAdditionalInfo()
    {
        return $this->IssuerAdditionalInfo;
    }

    /**
     * дополнительная информация о выдавшем доверенность
     *
     * Generated from protobuf field <code>string IssuerAdditionalInfo = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setIssuerAdditionalInfo($var)
    {
        GPBUtil::checkString($var, True);
        $this->IssuerAdditionalInfo = $var;

        return $this;
    }

    /**
     * лицо, получившее доверенность
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.Official RecipientPerson = 6;</code>
     * @return \Diadoc\Proto\Invoicing\Official
     */
    public function getRecipientPerson()
    {
        return $this->RecipientPerson;
    }

    /**
     * лицо, получившее доверенность
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.Official RecipientPerson = 6;</code>
     * @param \Diadoc\Proto\Invoicing\Official $var
     * @return $this
     */
    public function setRecipientPerson($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Invoicing\Official::class);
        $this->RecipientPerson = $var;

        return $this;
    }

    /**
     * дополнительная информация о получившем доверенность
     *
     * Generated from protobuf field <code>string RecipientAdditionalInfo = 7;</code>
     * @return string
     */
    public function getRecipientAdditionalInfo()
    {
        return $this->RecipientAdditionalInfo;
    }

    /**
     * дополнительная информация о получившем доверенность
     *
     * Generated from protobuf field <code>string RecipientAdditionalInfo = 7;</code>
     * @param string $var
     * @return $this
     */
    public function setRecipientAdditionalInfo($var)
    {
        GPBUtil::checkString($var, True);
        $this->RecipientAdditionalInfo = $var;

        return $this;
    }

}

