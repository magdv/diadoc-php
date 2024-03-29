<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Invoicing/UniversalTransferDocumentInfo.proto

namespace Diadoc\Proto\Invoicing;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Invoicing.TransferInfo</code>
 */
class TransferInfo extends \Google\Protobuf\Internal\Message
{
    /**
     * Содержание операции // СодОпер
     *
     * Generated from protobuf field <code>string OperationInfo = 1;</code>
     */
    private $OperationInfo = '';
    /**
     * Вид операции // ВидОпер
     *
     * Generated from protobuf field <code>string OperationType = 2;</code>
     */
    private $OperationType = '';
    /**
     * Дата отгрузки // ДатаПер
     *
     * Generated from protobuf field <code>string TransferDate = 3;</code>
     */
    private $TransferDate = '';
    /**
     * Основание отгрузки //ОснПер
     *
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Invoicing.TransferBase TransferBase = 4;</code>
     */
    private $TransferBase;
    /**
     * Сведения о транспортировке и грузе // СвТранГруз
     *
     * Generated from protobuf field <code>string TransferTextInfo = 5;</code>
     */
    private $TransferTextInfo = '';
    /**
     * Транспортная накладная //ТранНакл
     *
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Invoicing.Waybill Waybill = 6;</code>
     */
    private $Waybill;
    /**
     * Перевозчик // Перевозчик
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.Organizations.ExtendedOrganizationInfo Carrier = 7;</code>
     */
    private $Carrier = null;
    /**
     * Работник организации продавца //РабОргПрод
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.Employee Employee = 8;</code>
     */
    private $Employee = null;
    /**
     * Иное лицо //ИнЛицо
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.OtherIssuer OtherIssuer = 9;</code>
     */
    private $OtherIssuer = null;
    /**
     * Дата передачи вещи, изготовленной по договору //ДатаПерВещ
     *
     * Generated from protobuf field <code>string CreatedThingTransferDate = 10;</code>
     */
    private $CreatedThingTransferDate = '';
    /**
     * Сведения о передаче, изготовленной по договору //СвПерВещ
     *
     * Generated from protobuf field <code>string CreatedThingInfo = 11;</code>
     */
    private $CreatedThingInfo = '';
    /**
     * Информационное поле документа // ИнфПолФХЖ3
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.AdditionalInfoId AdditionalInfoId = 12;</code>
     */
    private $AdditionalInfoId = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $OperationInfo
     *           Содержание операции // СодОпер
     *     @type string $OperationType
     *           Вид операции // ВидОпер
     *     @type string $TransferDate
     *           Дата отгрузки // ДатаПер
     *     @type \Diadoc\Proto\Invoicing\TransferBase[]|\Google\Protobuf\Internal\RepeatedField $TransferBase
     *           Основание отгрузки //ОснПер
     *     @type string $TransferTextInfo
     *           Сведения о транспортировке и грузе // СвТранГруз
     *     @type \Diadoc\Proto\Invoicing\Waybill[]|\Google\Protobuf\Internal\RepeatedField $Waybill
     *           Транспортная накладная //ТранНакл
     *     @type \Diadoc\Proto\Invoicing\Organizations\ExtendedOrganizationInfo $Carrier
     *           Перевозчик // Перевозчик
     *     @type \Diadoc\Proto\Invoicing\Employee $Employee
     *           Работник организации продавца //РабОргПрод
     *     @type \Diadoc\Proto\Invoicing\OtherIssuer $OtherIssuer
     *           Иное лицо //ИнЛицо
     *     @type string $CreatedThingTransferDate
     *           Дата передачи вещи, изготовленной по договору //ДатаПерВещ
     *     @type string $CreatedThingInfo
     *           Сведения о передаче, изготовленной по договору //СвПерВещ
     *     @type \Diadoc\Proto\Invoicing\AdditionalInfoId $AdditionalInfoId
     *           Информационное поле документа // ИнфПолФХЖ3
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Invoicing\UniversalTransferDocumentInfo::initOnce();
        parent::__construct($data);
    }

    /**
     * Содержание операции // СодОпер
     *
     * Generated from protobuf field <code>string OperationInfo = 1;</code>
     * @return string
     */
    public function getOperationInfo()
    {
        return $this->OperationInfo;
    }

    /**
     * Содержание операции // СодОпер
     *
     * Generated from protobuf field <code>string OperationInfo = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setOperationInfo($var)
    {
        GPBUtil::checkString($var, True);
        $this->OperationInfo = $var;

        return $this;
    }

    /**
     * Вид операции // ВидОпер
     *
     * Generated from protobuf field <code>string OperationType = 2;</code>
     * @return string
     */
    public function getOperationType()
    {
        return $this->OperationType;
    }

    /**
     * Вид операции // ВидОпер
     *
     * Generated from protobuf field <code>string OperationType = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setOperationType($var)
    {
        GPBUtil::checkString($var, True);
        $this->OperationType = $var;

        return $this;
    }

    /**
     * Дата отгрузки // ДатаПер
     *
     * Generated from protobuf field <code>string TransferDate = 3;</code>
     * @return string
     */
    public function getTransferDate()
    {
        return $this->TransferDate;
    }

    /**
     * Дата отгрузки // ДатаПер
     *
     * Generated from protobuf field <code>string TransferDate = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setTransferDate($var)
    {
        GPBUtil::checkString($var, True);
        $this->TransferDate = $var;

        return $this;
    }

    /**
     * Основание отгрузки //ОснПер
     *
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Invoicing.TransferBase TransferBase = 4;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getTransferBase()
    {
        return $this->TransferBase;
    }

    /**
     * Основание отгрузки //ОснПер
     *
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Invoicing.TransferBase TransferBase = 4;</code>
     * @param \Diadoc\Proto\Invoicing\TransferBase[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setTransferBase($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Diadoc\Proto\Invoicing\TransferBase::class);
        $this->TransferBase = $arr;

        return $this;
    }

    /**
     * Сведения о транспортировке и грузе // СвТранГруз
     *
     * Generated from protobuf field <code>string TransferTextInfo = 5;</code>
     * @return string
     */
    public function getTransferTextInfo()
    {
        return $this->TransferTextInfo;
    }

    /**
     * Сведения о транспортировке и грузе // СвТранГруз
     *
     * Generated from protobuf field <code>string TransferTextInfo = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setTransferTextInfo($var)
    {
        GPBUtil::checkString($var, True);
        $this->TransferTextInfo = $var;

        return $this;
    }

    /**
     * Транспортная накладная //ТранНакл
     *
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Invoicing.Waybill Waybill = 6;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getWaybill()
    {
        return $this->Waybill;
    }

    /**
     * Транспортная накладная //ТранНакл
     *
     * Generated from protobuf field <code>repeated .Diadoc.Proto.Invoicing.Waybill Waybill = 6;</code>
     * @param \Diadoc\Proto\Invoicing\Waybill[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setWaybill($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Diadoc\Proto\Invoicing\Waybill::class);
        $this->Waybill = $arr;

        return $this;
    }

    /**
     * Перевозчик // Перевозчик
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.Organizations.ExtendedOrganizationInfo Carrier = 7;</code>
     * @return \Diadoc\Proto\Invoicing\Organizations\ExtendedOrganizationInfo
     */
    public function getCarrier()
    {
        return $this->Carrier;
    }

    /**
     * Перевозчик // Перевозчик
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.Organizations.ExtendedOrganizationInfo Carrier = 7;</code>
     * @param \Diadoc\Proto\Invoicing\Organizations\ExtendedOrganizationInfo $var
     * @return $this
     */
    public function setCarrier($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Invoicing\Organizations\ExtendedOrganizationInfo::class);
        $this->Carrier = $var;

        return $this;
    }

    /**
     * Работник организации продавца //РабОргПрод
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.Employee Employee = 8;</code>
     * @return \Diadoc\Proto\Invoicing\Employee
     */
    public function getEmployee()
    {
        return $this->Employee;
    }

    /**
     * Работник организации продавца //РабОргПрод
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.Employee Employee = 8;</code>
     * @param \Diadoc\Proto\Invoicing\Employee $var
     * @return $this
     */
    public function setEmployee($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Invoicing\Employee::class);
        $this->Employee = $var;

        return $this;
    }

    /**
     * Иное лицо //ИнЛицо
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.OtherIssuer OtherIssuer = 9;</code>
     * @return \Diadoc\Proto\Invoicing\OtherIssuer
     */
    public function getOtherIssuer()
    {
        return $this->OtherIssuer;
    }

    /**
     * Иное лицо //ИнЛицо
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.OtherIssuer OtherIssuer = 9;</code>
     * @param \Diadoc\Proto\Invoicing\OtherIssuer $var
     * @return $this
     */
    public function setOtherIssuer($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Invoicing\OtherIssuer::class);
        $this->OtherIssuer = $var;

        return $this;
    }

    /**
     * Дата передачи вещи, изготовленной по договору //ДатаПерВещ
     *
     * Generated from protobuf field <code>string CreatedThingTransferDate = 10;</code>
     * @return string
     */
    public function getCreatedThingTransferDate()
    {
        return $this->CreatedThingTransferDate;
    }

    /**
     * Дата передачи вещи, изготовленной по договору //ДатаПерВещ
     *
     * Generated from protobuf field <code>string CreatedThingTransferDate = 10;</code>
     * @param string $var
     * @return $this
     */
    public function setCreatedThingTransferDate($var)
    {
        GPBUtil::checkString($var, True);
        $this->CreatedThingTransferDate = $var;

        return $this;
    }

    /**
     * Сведения о передаче, изготовленной по договору //СвПерВещ
     *
     * Generated from protobuf field <code>string CreatedThingInfo = 11;</code>
     * @return string
     */
    public function getCreatedThingInfo()
    {
        return $this->CreatedThingInfo;
    }

    /**
     * Сведения о передаче, изготовленной по договору //СвПерВещ
     *
     * Generated from protobuf field <code>string CreatedThingInfo = 11;</code>
     * @param string $var
     * @return $this
     */
    public function setCreatedThingInfo($var)
    {
        GPBUtil::checkString($var, True);
        $this->CreatedThingInfo = $var;

        return $this;
    }

    /**
     * Информационное поле документа // ИнфПолФХЖ3
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.AdditionalInfoId AdditionalInfoId = 12;</code>
     * @return \Diadoc\Proto\Invoicing\AdditionalInfoId
     */
    public function getAdditionalInfoId()
    {
        return $this->AdditionalInfoId;
    }

    /**
     * Информационное поле документа // ИнфПолФХЖ3
     *
     * Generated from protobuf field <code>.Diadoc.Proto.Invoicing.AdditionalInfoId AdditionalInfoId = 12;</code>
     * @param \Diadoc\Proto\Invoicing\AdditionalInfoId $var
     * @return $this
     */
    public function setAdditionalInfoId($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Invoicing\AdditionalInfoId::class);
        $this->AdditionalInfoId = $var;

        return $this;
    }

}

