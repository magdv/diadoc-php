<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Organization.proto

namespace Diadoc\Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Department</code>
 */
class Department extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string DepartmentId = 1;</code>
     */
    private $DepartmentId = '';
    /**
     * Generated from protobuf field <code>string ParentDepartmentId = 2;</code>
     */
    private $ParentDepartmentId = '';
    /**
     * Generated from protobuf field <code>string Name = 3;</code>
     */
    private $Name = '';
    /**
     * Generated from protobuf field <code>string Abbreviation = 4;</code>
     */
    private $Abbreviation = '';
    /**
     * Generated from protobuf field <code>string Kpp = 5;</code>
     */
    private $Kpp = '';
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Address Address = 6;</code>
     */
    private $Address = null;
    /**
     * Generated from protobuf field <code>bool IsDisabled = 7;</code>
     */
    private $IsDisabled = false;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $DepartmentId
     *     @type string $ParentDepartmentId
     *     @type string $Name
     *     @type string $Abbreviation
     *     @type string $Kpp
     *     @type \Diadoc\Proto\Address $Address
     *     @type bool $IsDisabled
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Organization::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string DepartmentId = 1;</code>
     * @return string
     */
    public function getDepartmentId()
    {
        return $this->DepartmentId;
    }

    /**
     * Generated from protobuf field <code>string DepartmentId = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setDepartmentId($var)
    {
        GPBUtil::checkString($var, True);
        $this->DepartmentId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string ParentDepartmentId = 2;</code>
     * @return string
     */
    public function getParentDepartmentId()
    {
        return $this->ParentDepartmentId;
    }

    /**
     * Generated from protobuf field <code>string ParentDepartmentId = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setParentDepartmentId($var)
    {
        GPBUtil::checkString($var, True);
        $this->ParentDepartmentId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string Name = 3;</code>
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Generated from protobuf field <code>string Name = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkString($var, True);
        $this->Name = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string Abbreviation = 4;</code>
     * @return string
     */
    public function getAbbreviation()
    {
        return $this->Abbreviation;
    }

    /**
     * Generated from protobuf field <code>string Abbreviation = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setAbbreviation($var)
    {
        GPBUtil::checkString($var, True);
        $this->Abbreviation = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string Kpp = 5;</code>
     * @return string
     */
    public function getKpp()
    {
        return $this->Kpp;
    }

    /**
     * Generated from protobuf field <code>string Kpp = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setKpp($var)
    {
        GPBUtil::checkString($var, True);
        $this->Kpp = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Address Address = 6;</code>
     * @return \Diadoc\Proto\Address
     */
    public function getAddress()
    {
        return $this->Address;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Address Address = 6;</code>
     * @param \Diadoc\Proto\Address $var
     * @return $this
     */
    public function setAddress($var)
    {
        GPBUtil::checkMessage($var, \Diadoc\Proto\Address::class);
        $this->Address = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool IsDisabled = 7;</code>
     * @return bool
     */
    public function getIsDisabled()
    {
        return $this->IsDisabled;
    }

    /**
     * Generated from protobuf field <code>bool IsDisabled = 7;</code>
     * @param bool $var
     * @return $this
     */
    public function setIsDisabled($var)
    {
        GPBUtil::checkBool($var);
        $this->IsDisabled = $var;

        return $this;
    }

}

