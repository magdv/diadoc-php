<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Documents/ReceiptStatus.proto

namespace Diadoc\Proto\Documents;

/**
 * Protobuf type <code>Diadoc.Proto.Documents.ReceiptStatus</code>
 */
class ReceiptStatus
{
    /**
     * Reserved state to report to legacy client for new statuses
     *
     * Generated from protobuf enum <code>UnknownReceiptStatus = 0;</code>
     */
    const UnknownReceiptStatus = 0;
    /**
     * Generated from protobuf enum <code>ReceiptStatusNone = 1;</code>
     */
    const ReceiptStatusNone = 1;
    /**
     * Generated from protobuf enum <code>ReceiptStatusFinished = 2;</code>
     */
    const ReceiptStatusFinished = 2;
    /**
     * Generated from protobuf enum <code>ReceiptStatusHaveToCreateReceipt = 3;</code>
     */
    const ReceiptStatusHaveToCreateReceipt = 3;
    /**
     * Generated from protobuf enum <code>ReceiptStatusWaitingForReceipt = 4;</code>
     */
    const ReceiptStatusWaitingForReceipt = 4;
}

