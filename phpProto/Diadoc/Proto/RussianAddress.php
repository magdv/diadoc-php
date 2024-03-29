<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Address.proto

namespace Diadoc\Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.RussianAddress</code>
 */
class RussianAddress extends \Google\Protobuf\Internal\Message
{
    /**
     * индекс
     *
     * Generated from protobuf field <code>string ZipCode = 1;</code>
     */
    private $ZipCode = '';
    /**
     * регион (код)
     *
     * Generated from protobuf field <code>string Region = 2;</code>
     */
    private $Region = '';
    /**
     * район
     *
     * Generated from protobuf field <code>string Territory = 3;</code>
     */
    private $Territory = '';
    /**
     * город
     *
     * Generated from protobuf field <code>string City = 4;</code>
     */
    private $City = '';
    /**
     * населенный пункт
     *
     * Generated from protobuf field <code>string Locality = 5;</code>
     */
    private $Locality = '';
    /**
     * улица
     *
     * Generated from protobuf field <code>string Street = 6;</code>
     */
    private $Street = '';
    /**
     * дом
     *
     * Generated from protobuf field <code>string Building = 7;</code>
     */
    private $Building = '';
    /**
     * корпус
     *
     * Generated from protobuf field <code>string Block = 8;</code>
     */
    private $Block = '';
    /**
     * квартира
     *
     * Generated from protobuf field <code>string Apartment = 9;</code>
     */
    private $Apartment = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $ZipCode
     *           индекс
     *     @type string $Region
     *           регион (код)
     *     @type string $Territory
     *           район
     *     @type string $City
     *           город
     *     @type string $Locality
     *           населенный пункт
     *     @type string $Street
     *           улица
     *     @type string $Building
     *           дом
     *     @type string $Block
     *           корпус
     *     @type string $Apartment
     *           квартира
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Address::initOnce();
        parent::__construct($data);
    }

    /**
     * индекс
     *
     * Generated from protobuf field <code>string ZipCode = 1;</code>
     * @return string
     */
    public function getZipCode()
    {
        return $this->ZipCode;
    }

    /**
     * индекс
     *
     * Generated from protobuf field <code>string ZipCode = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setZipCode($var)
    {
        GPBUtil::checkString($var, True);
        $this->ZipCode = $var;

        return $this;
    }

    /**
     * регион (код)
     *
     * Generated from protobuf field <code>string Region = 2;</code>
     * @return string
     */
    public function getRegion()
    {
        return $this->Region;
    }

    /**
     * регион (код)
     *
     * Generated from protobuf field <code>string Region = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setRegion($var)
    {
        GPBUtil::checkString($var, True);
        $this->Region = $var;

        return $this;
    }

    /**
     * район
     *
     * Generated from protobuf field <code>string Territory = 3;</code>
     * @return string
     */
    public function getTerritory()
    {
        return $this->Territory;
    }

    /**
     * район
     *
     * Generated from protobuf field <code>string Territory = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setTerritory($var)
    {
        GPBUtil::checkString($var, True);
        $this->Territory = $var;

        return $this;
    }

    /**
     * город
     *
     * Generated from protobuf field <code>string City = 4;</code>
     * @return string
     */
    public function getCity()
    {
        return $this->City;
    }

    /**
     * город
     *
     * Generated from protobuf field <code>string City = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setCity($var)
    {
        GPBUtil::checkString($var, True);
        $this->City = $var;

        return $this;
    }

    /**
     * населенный пункт
     *
     * Generated from protobuf field <code>string Locality = 5;</code>
     * @return string
     */
    public function getLocality()
    {
        return $this->Locality;
    }

    /**
     * населенный пункт
     *
     * Generated from protobuf field <code>string Locality = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setLocality($var)
    {
        GPBUtil::checkString($var, True);
        $this->Locality = $var;

        return $this;
    }

    /**
     * улица
     *
     * Generated from protobuf field <code>string Street = 6;</code>
     * @return string
     */
    public function getStreet()
    {
        return $this->Street;
    }

    /**
     * улица
     *
     * Generated from protobuf field <code>string Street = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setStreet($var)
    {
        GPBUtil::checkString($var, True);
        $this->Street = $var;

        return $this;
    }

    /**
     * дом
     *
     * Generated from protobuf field <code>string Building = 7;</code>
     * @return string
     */
    public function getBuilding()
    {
        return $this->Building;
    }

    /**
     * дом
     *
     * Generated from protobuf field <code>string Building = 7;</code>
     * @param string $var
     * @return $this
     */
    public function setBuilding($var)
    {
        GPBUtil::checkString($var, True);
        $this->Building = $var;

        return $this;
    }

    /**
     * корпус
     *
     * Generated from protobuf field <code>string Block = 8;</code>
     * @return string
     */
    public function getBlock()
    {
        return $this->Block;
    }

    /**
     * корпус
     *
     * Generated from protobuf field <code>string Block = 8;</code>
     * @param string $var
     * @return $this
     */
    public function setBlock($var)
    {
        GPBUtil::checkString($var, True);
        $this->Block = $var;

        return $this;
    }

    /**
     * квартира
     *
     * Generated from protobuf field <code>string Apartment = 9;</code>
     * @return string
     */
    public function getApartment()
    {
        return $this->Apartment;
    }

    /**
     * квартира
     *
     * Generated from protobuf field <code>string Apartment = 9;</code>
     * @param string $var
     * @return $this
     */
    public function setApartment($var)
    {
        GPBUtil::checkString($var, True);
        $this->Apartment = $var;

        return $this;
    }

}

