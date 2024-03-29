<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Docflow/Docflow.proto

namespace Diadoc\Proto\Docflow;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Diadoc.Proto.Docflow.DocflowStatusModel</code>
 */
class DocflowStatusModel extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.DocflowStatusSeverity Severity = 1;</code>
     */
    private $Severity = 0;
    /**
     * Generated from protobuf field <code>string StatusText = 2;</code>
     */
    private $StatusText = '';
    /**
     * Generated from protobuf field <code>string StatusHint = 3;</code>
     */
    private $StatusHint = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $Severity
     *     @type string $StatusText
     *     @type string $StatusHint
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Docflow\Docflow::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.DocflowStatusSeverity Severity = 1;</code>
     * @return int
     */
    public function getSeverity()
    {
        return $this->Severity;
    }

    /**
     * Generated from protobuf field <code>.Diadoc.Proto.Docflow.DocflowStatusSeverity Severity = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setSeverity($var)
    {
        GPBUtil::checkEnum($var, \Diadoc\Proto\Docflow\DocflowStatusSeverity::class);
        $this->Severity = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string StatusText = 2;</code>
     * @return string
     */
    public function getStatusText()
    {
        return $this->StatusText;
    }

    /**
     * Generated from protobuf field <code>string StatusText = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setStatusText($var)
    {
        GPBUtil::checkString($var, True);
        $this->StatusText = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string StatusHint = 3;</code>
     * @return string
     */
    public function getStatusHint()
    {
        return $this->StatusHint;
    }

    /**
     * Generated from protobuf field <code>string StatusHint = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setStatusHint($var)
    {
        GPBUtil::checkString($var, True);
        $this->StatusHint = $var;

        return $this;
    }

}

