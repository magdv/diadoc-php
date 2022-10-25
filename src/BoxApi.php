<?php

namespace MagDv\Diadoc;

use MagDv\Diadoc\Exception\DiadocApiException;
use DateTime;
use Diadoc\Proto\Box;
use Diadoc\Proto\Docflow\GetDocflowBatchRequest;
use Diadoc\Proto\Docflow\GetDocflowBatchResponse;
use Diadoc\Proto\Docflow\GetDocflowEventsResponse;
use Diadoc\Proto\Docflow\GetDocflowsByPacketIdResponse;
use Diadoc\Proto\Docflow\SearchDocflowsResponse;
use Diadoc\Proto\Docflow\SearchScope;
use Diadoc\Proto\DocumentId;
use Diadoc\Proto\Documents\Document;
use Diadoc\Proto\Documents\DocumentList;
use Diadoc\Proto\Events\BoxEvent;
use Diadoc\Proto\Events\BoxEventList;
use Diadoc\Proto\Events\Message;
use Diadoc\Proto\Organization;
use Diadoc\Proto\SortDirection;
use MagDv\Diadoc\Filter\DocumentsFilter;

class BoxApi
{
    /** @var  DiadocApi */
    private $diadocApi;
    /** @var  string */
    private $boxId;
    /** @var  OrganizationApi */
    private $organizationApi;

    /**
     * @param DiadocApi $diadocApi
     * @param string $boxId
     */
    public function __construct(DiadocApi $diadocApi, string $boxId)
    {
        $this->diadocApi = $diadocApi;
        $this->boxId = $boxId;
    }

    /**
     * @return DiadocApi
     */
    public function getDiadocApi(): DiadocApi
    {
        return $this->diadocApi;
    }

    /**
     * @return Box
     */
    public function getBox(): Box
    {
        return $this->diadocApi->getBox($this->boxId);
    }

    /**
     * @return Organization
     */
    public function getOrganization(): Organization
    {
        return $this->getBox()->getOrganization();
    }

    /**
     * @return OrganizationApi
     */
    public function getOrganizationApi(): OrganizationApi
    {
        if (!$this->organizationApi) {
            $this->organizationApi = new OrganizationApi(
                $this->diadocApi,
                $this->getOrganization()->getOrgId()
            );
        }

        return $this->organizationApi;
    }

    /**
     * @param $messageId
     * @param $entityId
     *
     * @return mixed
     * @throws DiadocApiException
     */
    public function getEntityContent($messageId, $entityId): string
    {
        return $this->diadocApi->getEntityContent($this->boxId, $messageId, $entityId);
    }

    /**
     * @param string $messageId
     * @param string|null $entityId
     * @param string|null $originalSignature
     *
     * @return Message
     * @throws DiadocApiException
     */
    public function getMessage(string $messageId, string  $entityId = null, string  $originalSignature = null): Message
    {
        return $this->diadocApi->getMessage($this->boxId, $messageId, $entityId, $originalSignature);
    }

    /**
     * @param string $messageId
     * @param string|null $documentId
     *
     * @return bool
     * @throws DiadocApiException
     */
    public function delete(string $messageId, string  $documentId = null): bool
    {
        return $this->diadocApi->delete($this->boxId, $messageId, $documentId);
    }

    public function forwardDocument(string $toBoxId, DocumentId $documentId): string
    {
        return $this->diadocApi->forwardDocument($this->boxId, $toBoxId, $documentId);
    }

    /**
     * @param string $messageId
     * @param string $entityId
     *
     * @return Document
     * @throws DiadocApiException
     */
    public function getDocument(string $messageId, string $entityId): Document
    {
        return $this->diadocApi->getDocument($this->boxId, $messageId, $entityId);
    }

    /**
     * @param DocumentsFilter|null $documentsFilter
     * @param SortDirection|null $sortDirection
     * @param null $afterIndexKey
     *
     * @return DocumentList
     */
    public function getDocuments(DocumentsFilter $documentsFilter = null, SortDirection $sortDirection = null, $afterIndexKey = null): DocumentList
    {
        return $this->diadocApi->getDocuments($this->boxId, $documentsFilter, $sortDirection, $afterIndexKey);
    }

    /**
     * @param GetDocflowBatchRequest $batchRequest
     * @return GetDocflowBatchResponse
     * @throws DiadocApiException
     */
    public function getDocflows(GetDocflowBatchRequest $batchRequest): GetDocflowBatchResponse
    {
        return $this->diadocApi->getDocflows($this->boxId, $batchRequest);
    }

    /**
     * @param string $packetId
     * @param bool $injectEntityContent
     * @param int|null $afterIndexKey
     * @param int $count
     *
     * @return GetDocflowsByPacketIdResponse
     * @throws DiadocApiException
     */
    public function getDocflowsByPacketId(string $packetId, bool $injectEntityContent = false, ?int $afterIndexKey = null, int $count = 100): GetDocflowsByPacketIdResponse
    {
        return $this->diadocApi->getDocflowsByPacketId($this->boxId, $packetId, $injectEntityContent, $afterIndexKey, $count);
    }

    /**
     * @param string $queryString
     * @param SearchScope|null $searchScope
     * @param int|null $firstIndex
     * @param int $count
     *
     * @return SearchDocflowsResponse
     * @throws DiadocApiException
     */
    public function searchDocflows(string $queryString, SearchScope $searchScope = null, int $firstIndex = null, int $count = 100): SearchDocflowsResponse
    {
        return $this->diadocApi->searchDocflows($this->boxId, $queryString, $searchScope, $firstIndex, $count);
    }

    public function getDocflowEvents(
        DateTime $from = null,
        DateTime $to = null,
        SortDirection $sortDirection = null,
        bool $populateDocuments = false,
        bool $populatePreviousDocumentStates = false,
        bool $injectEntityContent = false,
        int $afterIndexKey = null
    ): GetDocflowEventsResponse {
        return $this->diadocApi->getDocflowEvents(
            $this->boxId,
            $from,
            $to,
            $sortDirection,
            $populateDocuments,
            $populatePreviousDocumentStates,
            $injectEntityContent,
            $afterIndexKey
        );
    }

    /**
     * @param string $eventId
     *
     * @return BoxEvent
     * @throws DiadocApiException
     */
    public function getEvent(string $eventId): BoxEvent
    {
        return $this->diadocApi->getEvent($this->boxId, $eventId);
    }

    /**
     * @param int|null $afterEventId
     * @return BoxEventList
     */
    public function getNewEvents(int $afterEventId = null): BoxEventList
    {
        return $this->diadocApi->getNewEvents($this->boxId, $afterEventId);
    }
}
