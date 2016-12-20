<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: Documents/Document.proto

namespace Diadoc\Proto\Documents;

/**
 * Protobuf type <code>Diadoc.Proto.Documents.RevocationStatus</code>
 */
class RevocationStatus
{
    /**
     * Reserved status to report to legacy clients for newly introduced statuses
     *
     * Generated from protobuf enum <code>UnknownRevocationStatus = 0;</code>
     */
    const UnknownRevocationStatus = 0;
    /**
     * Generated from protobuf enum <code>RevocationStatusNone = 1;</code>
     */
    const RevocationStatusNone = 1;
    /**
     * Generated from protobuf enum <code>RevocationIsRequestedByMe = 2;</code>
     */
    const RevocationIsRequestedByMe = 2;
    /**
     * Generated from protobuf enum <code>RequestsMyRevocation = 3;</code>
     */
    const RequestsMyRevocation = 3;
    /**
     * Generated from protobuf enum <code>RevocationAccepted = 4;</code>
     */
    const RevocationAccepted = 4;
    /**
     * Generated from protobuf enum <code>RevocationRejected = 5;</code>
     */
    const RevocationRejected = 5;
}

