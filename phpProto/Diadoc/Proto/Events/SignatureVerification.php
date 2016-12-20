<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Events/DiadocMessage-PostApi.proto

namespace Diadoc\Proto\Events;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Events.SignatureVerification</code>
 */
class SignatureVerification extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string InitialDocumentId = 1;</code>
     */
    private $InitialDocumentId = '';
    /**
     * Generated from protobuf field <code>bool IsValid = 2;</code>
     */
    private $IsValid = false;
    /**
     * Generated from protobuf field <code>string ErrorMessage = 3;</code>
     */
    private $ErrorMessage = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $InitialDocumentId
     *     @type bool $IsValid
     *     @type string $ErrorMessage
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Events\DiadocMessagePostApi::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string InitialDocumentId = 1;</code>
     * @return string
     */
    public function getInitialDocumentId()
    {
        return $this->InitialDocumentId;
    }

    /**
     * Generated from protobuf field <code>string InitialDocumentId = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setInitialDocumentId($var)
    {
        GPBUtil::checkString($var, True);
        $this->InitialDocumentId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool IsValid = 2;</code>
     * @return bool
     */
    public function getIsValid()
    {
        return $this->IsValid;
    }

    /**
     * Generated from protobuf field <code>bool IsValid = 2;</code>
     * @param bool $var
     * @return $this
     */
    public function setIsValid($var)
    {
        GPBUtil::checkBool($var);
        $this->IsValid = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string ErrorMessage = 3;</code>
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->ErrorMessage;
    }

    /**
     * Generated from protobuf field <code>string ErrorMessage = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setErrorMessage($var)
    {
        GPBUtil::checkString($var, True);
        $this->ErrorMessage = $var;

        return $this;
    }

}

