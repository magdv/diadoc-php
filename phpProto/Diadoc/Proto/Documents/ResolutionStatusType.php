<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Documents/Document.proto

namespace Diadoc\Proto\Documents;

/**
 * Protobuf type <code>Diadoc.Proto.Documents.ResolutionStatusType</code>
 */
class ResolutionStatusType
{
    /**
     * Generated from protobuf enum <code>None = 0;</code>
     */
    const None = 0;
    /**
     * Reserved status to report to legacy clients for newly introduced statuses
     *
     * Generated from protobuf enum <code>UnknownResolutionStatus = -1;</code>
     */
    const UnknownResolutionStatus = -1;
    /**
     * Generated from protobuf enum <code>Approved = 1;</code>
     */
    const Approved = 1;
    /**
     * Generated from protobuf enum <code>Disapproved = 2;</code>
     */
    const Disapproved = 2;
    /**
     * Generated from protobuf enum <code>ApprovementRequested = 3;</code>
     */
    const ApprovementRequested = 3;
    /**
     * Generated from protobuf enum <code>SignatureRequested = 4;</code>
     */
    const SignatureRequested = 4;
    /**
     * Generated from protobuf enum <code>SignatureDenied = 5;</code>
     */
    const SignatureDenied = 5;
}

