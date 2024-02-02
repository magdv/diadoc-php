<?php

namespace MagDv\Diadoc;

use Diadoc\Proto\Documents\Types\GetDocumentTypesResponseV2;
use Diadoc\Proto\Events\SignedContent;
use Exception;
use DateTime;
use Diadoc\Proto\AcquireCounteragentRequest;
use Diadoc\Proto\AcquireCounteragentResult;
use Diadoc\Proto\AsyncMethodResult;
use Diadoc\Proto\Box;
use Diadoc\Proto\Counteragent;
use Diadoc\Proto\CounteragentCertificateList;
use Diadoc\Proto\CounteragentList;
use Diadoc\Proto\CounteragentStatus;
use Diadoc\Proto\Department;
use Diadoc\Proto\Docflow\GetDocflowBatchRequest;
use Diadoc\Proto\Docflow\GetDocflowBatchResponse;
use Diadoc\Proto\Docflow\GetDocflowEventsRequest;
use Diadoc\Proto\Docflow\GetDocflowEventsResponse;
use Diadoc\Proto\Docflow\GetDocflowsByPacketIdRequest;
use Diadoc\Proto\Docflow\GetDocflowsByPacketIdResponse;
use Diadoc\Proto\Docflow\SearchDocflowsRequest;
use Diadoc\Proto\Docflow\SearchDocflowsResponse;
use Diadoc\Proto\DocumentId;
use Diadoc\Proto\Documents\Document;
use Diadoc\Proto\Documents\DocumentList;
use Diadoc\Proto\Events\BoxEvent;
use Diadoc\Proto\Events\BoxEventList;
use Diadoc\Proto\Events\Message;
use Diadoc\Proto\Events\MessagePatch;
use Diadoc\Proto\Events\MessagePatchToPost;
use Diadoc\Proto\Events\MessageToPost;
use Diadoc\Proto\Forwarding\ForwardDocumentRequest;
use Diadoc\Proto\GetOrganizationsByInnListRequest;
use Diadoc\Proto\GetOrganizationsByInnListResponse;
use Diadoc\Proto\InvitationDocument;
use Diadoc\Proto\Organization;
use Diadoc\Proto\OrganizationList;
use Diadoc\Proto\OrganizationUserPermissions;
use Diadoc\Proto\OrganizationUsersList;
use Diadoc\Proto\RussianAddress;
use Diadoc\Proto\SortDirection;
use Diadoc\Proto\TimeBasedFilter;
use Diadoc\Proto\Timestamp;
use Diadoc\Proto\User;
use MagDv\Diadoc\Exception\DiadocApiException;
use MagDv\Diadoc\Exception\DiadocApiUnauthorizedException;
use MagDv\Diadoc\Filter\DocumentsFilter;
use MagDv\Diadoc\Helper\DateHelper;
use MagDv\Diadoc\Signer\Interfaces\SignerProviderInterface;

class DiadocApi
{
    /**
     * @var string
     */
    public const METHOD_GET = 'GET';

    /**
     * @var string
     */
    public const CONTENT_FORM_URL_ENCODED = 'application/x-www-form-urlencoded';

    /**
     * @var string
     */
    public const METHOD_POST = 'POST';

    // Authorization
    /**
     * @var string
     */
    public const RESOURCE_AUTHENTICATE = '/Authenticate';

    /**
     * @var string
     */
    public const RESOURCE_AUTHENTICATE_V2 = '/V2/Authenticate';

    /**
     * @var string
     */
    public const RESOURCE_GET_EXTERNAL_SERVICE_AUTH_INFO = '/GetExternalServiceAuthInfo';

    // Organizations
    /**
     * @var string
     */
    public const RESOURCE_GET_BOX = '/GetBox';

    /**
     * @var string
     */
    public const RESOURCE_GET_DEPARTMENT = '/GetDepartment';

    /**
     * @var string
     */
    public const RESOURCE_GET_MY_ORGANIZATION = '/GetMyOrganizations';

    /**
     * @var string
     */
    public const RESOURCE_GET_MY_PERMISSIONS = '/GetMyPermissions';

    /**
     * @var string
     */
    public const RESOURCE_GET_MY_USER = '/GetMyUser';

    /**
     * @var string
     */
    public const RESOURCE_GET_ORGANIZATION = '/GetOrganization';

    /**
     * @var string
     */
    public const RESOURCE_GET_ORGANIZATIONS_BY_INN_KPP = '/GetOrganizationsByInnKpp';

    /**
     * @var string
     */
    public const RESOURCE_GET_ORGANIZATIONS_BY_INN_LIST = '/GetOrganizationsByInnList';

    /**
     * @var string
     */
    public const RESOURCE_GET_ORGANIZATION_USERS = '/GetOrganizationUsers';

    /**
     * @var string
     */
    public const RESOURCE_PARSE_RUSSIAN_ADDRESS = '/ParseRussianAddress';

    // Counteragents
    /**
     * @var string
     */
    public const RESOURCE_ACQUIRE_COUNTERAGENTS = '/AcquireCounteragent';

    /**
     * @var string
     */
    public const RESOURCE_ACQUIRE_COUNTERAGENTS_V2 = '/V2/AcquireCounteragent';

    /**
     * @var string
     */
    public const RESOURCE_ACQUIRE_COUNTERAGENT_RESULT = '/AcquireCounteragentResult';

    /**
     * @var string
     */
    public const RESOURCE_BREAK_WITH_COUNTERAGENT = '/BreakWithCounteragent';

    /**
     * @var string
     */
    public const RESOURCE_GET_COUNTERAGENT = '/GetCounteragent';

    /**
     * @var string
     */
    public const RESOURCE_GET_COUNTERAGENT_V2 = '/V2/GetCounteragent';

    /**
     * @var string
     */
    public const RESOURCE_GET_COUNTERAGENTS = '/GetCounteragents';

    /**
     * @var string
     */
    public const RESOURCE_GET_COUNTERAGENTS_V2 = '/V2/GetCounteragents';

    /**
     * @var string
     */
    public const RESOURCE_GET_COUNTERAGENT_CERTIFICATES = '/GetCounteragentCertificates';

    // Messages
    /**
     * @var string
     */
    public const RESOURCE_GET_ENTITY_CONTENT = ' /V4/GetEntityContent';

    /**
     * @var string
     */
    public const RESOURCE_GET_MESSAGE = '/V3/GetMessage';

    /**
     * @var string
     */
    public const RESOURCE_POST_MESSAGE = '/V3/PostMessage';

    /**
     * @var string
     */
    public const RESOURCE_POST_MESSAGE_PATCH = '/V3/PostMessagePatch';

    // Documents
    /**
     * @var string
     */
    public const RESOURCE_DELETE = '/Delete';

    /**
     * @var string
     */
    public const RESOURCE_FORWARD_DOCUMENT = '/V2/ForwardDocument';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_ACCEPTANCE_CERTIFICATE_XML_FOR_BUYER = '/GenerateAcceptanceCertificateXmlForBuyer';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_ACCEPTANCE_CERTIFICATE_XML_FOR_SELLER = '/GenerateAcceptanceCertificateXmlForSeller';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_DOCUMENT_PROTOCOL = '/GenerateDocumentProtocol';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_DOCUMENT_ZIP = '/GenerateDocumentZip';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_FORWARDED_DOCUMENT_PROTOCOL = '/V2/GenerateForwardedDocumentProtocol';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_PRINT_FORM = '/GeneratePrintForm';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_PRINT_FORM_FROM_ATTACHMENT = '/GeneratePrintFormFromAttachment';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_REVOCATION_REQUEST_XML = '/GenerateRevocationRequestXml';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_SIGNATURE_REJECTION_XML = '/GenerateSignatureRejectionXml';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_TORG_12_XML_FOR_BUYER = '/GenerateTorg12XmlForBuyer';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_TORG_12_XML_FOR_SELLER = '/GenerateTorg12XmlForSeller';

    /**
     * @var string
     */
    public const RESOURCE_GET_DOCUMENT = '/V3/GetDocument';

    /**
     * @var string
     */
    public const RESOURCE_GET_DOCUMENTS = '/V3/GetDocuments';

    /**
     * @var string
     */
    public const RESOURCE_GET_DOCUMENT_TYPES = '/V2/GetDocumentTypes';

    /**
     * @var string
     */
    public const RESOURCE_GET_FORWARDED_DOCUMENT_EVENTS = '/V2/GetForwardedDocumentEvents';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_FORWARDED_DOCUMENT_PRINT_FORM = '/GenerateForwardedDocumentPrintForm';

    /**
     * @var string
     */
    public const RESOURCE_GET_FORWARDED_ENTITY_CONTENT = '/V2/GetForwardedEntityContent';

    /**
     * @var string
     */
    public const RESOURCE_GET_FORWARDED_DOCUMENT = '/V2/GetForwardedDocuments';

    /**
     * @var string
     */
    public const RESOURCE_GET_GENERATED_PRINT_FORM = '/GetGeneratedPrintForm';

    /**
     * @var string
     */
    public const RESOURCE_GET_RECOGNIZED = '/GetRecognized';

    /**
     * @var string
     */
    public const RESOURCE_MOVE_DOCUMENTS = '/MoveDocuments';

    /**
     * @var string
     */
    public const RESOURCE_PARSE_ACCEPTANCE_CERTIFICATE_SELLER_TITLE_XML = '/ParseAcceptanceCertificateSellerTitleXml';

    /**
     * @var string
     */
    public const RESOURCE_PARSE_REVOCATION_REQUEST_XML = '/ParseRevocationRequestXml';

    /**
     * @var string
     */
    public const RESOURCE_PARSE_SIGNATURE_REJECTION_XML = '/ParseSignatureRejectionXml';

    /**
     * @var string
     */
    public const RESOURCE_PARSE_TORG_12_SELLER_TITLE_XML = '/ParseTorg12SellerTitleXml';

    /**
     * @var string
     */
    public const RESOURCE_PREPARE_DOCUMENTS_TO_SIGN = '/PrepareDocumentsToSign';

    /**
     * @var string
     */
    public const RESOURCE_RECOGNIZE = '/Recognize';

    /**
     * @var string
     */
    public const RESOURCE_RECYCLE_DRAFT = '/RecycleDraft';

    /**
     * @var string
     */
    public const RESOURCE_RESTORE = '/Restore';

    /**
     * @var string
     */
    public const RESOURCE_SHELF_UPLOAD = '/ShelfUpload';

    /**
     * @var string
     */
    public const RESOURCE_SEND_DRAFT = '/SendDraft';

    // SF/ISF/KSF
    /**
     * @var string
     */
    public const RESOURCE_CAN_SEND_INVOICE = '/CanSendInvoice';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_INVOICE_XML = '/GenerateInvoiceXml';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_INVOICE_CORRECTION_REQUEST_XML = '/GenerateInvoiceCorrectionRequestXml';

    /**
     * @var string
     */
    public const RESOURCE_GENERATE_INVOICE_DOCUMENT_RECEIPT_XML = '/GenerateInvoiceDocumentReceiptXml';

    /**
     * @var string
     */
    public const RESOURCE_GET_INVOICE_CORRECTION_REQUEST_INFO = '/GetInvoiceCorrectionRequestInfo';

    /**
     * @var string
     */
    public const RESOURCE_PARSE_INVOICE_XML = '/ParseInvoiceXml';

    // Events
    /**
     * @var string
     */
    public const RESOURCE_GET_EVENT = '/V2/GetEvent';

    /**
     * @var string
     */
    public const RESOURCE_GET_NEW_EVENTS = '/V4/GetNewEvents';

    //Docflow API
    /**
     * @var string
     */
    public const RESOURCE_GET_DOCFLOWS = '/V2/GetDocflows';

    /**
     * @var string
     */
    public const RESOURCE_GET_DOCFLOWS_BY_PACKET_ID = '/V2/GetDocflowsByPacketId';

    /**
     * @var string
     */
    public const RESOURCE_SEARCH_DOCFLOWS = '/V2/SearchDocflows';

    /**
     * @var string
     */
    public const RESOURCE_GET_DOCFLOWS_EVENTS = '/V2/GetDocflowEvents';

    // Cloud sign
    /**
     * @var string
     */
    public const RESOURCE_CLOUD_SIGN = '/CloudSign';

    /**
     * @var string
     */
    public const RESOURCE_CLOUD_SIGN_CONFIRM = '/CloudSignConfirm';

    /**
     * @var string
     */
    public const RESOURCE_CLOUD_SIGN_CONFIRM_RESULT = '/CloudSignConfirmResult';

    /**
     * @var string
     */
    public const RESOURCE_CLOUD_SIGN_RESULT = '/CloudSignResult';

    /**
     * @var string
     */
    public const RESOURCE_AUTO_SIGN_RECEIPTS = '/AutoSignReceipts';

    /**
     * @var string
     */
    public const RESOURCE_AUTO_SIGN_RECEIPTS_RESULT = '/AutoSignReceiptsResult';

    private ?string $token = null;

    public function __construct(
        private string $ddauthApiClientId,
        private string $serviceUrl = 'https://diadoc-api.kontur.ru/',
        private ?SignerProviderInterface $signerProvider = null
    ) {
    }

    public function authenticateLogin(string $login, string $password): string
    {
        $response = $this->doRequest(
            self::RESOURCE_AUTHENTICATE,
            [],
            [
                'login' => $login,
                'password'  => $password
            ],
            self::METHOD_POST
        );

        $this->setToken($response);

        return $response;
    }

    private function buildRequestHeaders(?string $contentType = null): array
    {
        $header = sprintf('DiadocAuth ddauth_api_client_id=%s', $this->ddauthApiClientId);
        if ($token = $this->getToken()) {
            $header .= sprintf(', ddauth_token=%s', $token);
        }

        return ['Authorization: ' . $header, "Content-type: " . ($contentType ?: 'application/x-protobuf')];
    }

    /**
     * @throws DiadocApiException
     * @throws DiadocApiUnauthorizedException
     */
    protected function doRequest(string $resource, mixed $postData = [], array $queryParams = [], string $method = self::METHOD_GET, ?string $contentType = null): string
    {
        if (!$this->getToken() && !in_array($resource, [self::RESOURCE_AUTHENTICATE, self::RESOURCE_AUTHENTICATE_V2], true)) {
            throw new Exception('Unauthorized request');
        }

        $uri = sprintf(
            '%s%s?%s',
            $this->serviceUrl,
            $resource,
            http_build_query($queryParams)
        );

        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildRequestHeaders($contentType));

        if ($method === self::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postData) ? http_build_query($postData) : $postData);
        } elseif ($method === self::METHOD_GET) {
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch) !== 0) {
            throw new DiadocApiException(sprintf('Curl error: (%s) %s', curl_errno($ch), curl_error($ch)), curl_errno($ch));
        }

        if (!($httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE)) || ($httpCode !== 200 && $httpCode !== 204)) {
            $message = sprintf('Curl error http code: (%s) %s', $httpCode, $response);
            if ($httpCode === 401) {
                throw new DiadocApiUnauthorizedException($message, $httpCode);
            }

            throw new DiadocApiException($message, $httpCode);
        }

        curl_close($ch);

        if ($response === false) {
            throw new DiadocApiException('Diadoc request error false returned');
        }

        return $response;
    }


    /**
     * @throws DiadocApiException
     */
    public function getBox(string $boxId): Box
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_BOX,
            [],
            [
                'boxId' => $boxId
            ]
        );

        $box = new Box();
        $box->mergeFromString($response);

        return $box;
    }

    /**
     *
     * @return Department| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getDepartment(string $orgId, string $departmentId): Department|\Google\Protobuf\Internal\Message
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_DEPARTMENT,
            [],
            [
                'orgId' => $orgId,
                'departmentId' => $departmentId
            ]
        );

        $department = new Department();
        $department->mergeFromString($response);

        return $department;
    }

    /**
     * @return OrganizationList| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getMyOrganizations(): OrganizationList
    {
        $response = $this->doRequest(self::RESOURCE_GET_MY_ORGANIZATION);

        $list = new OrganizationList();
        $list->mergeFromString($response);

        return $list;
    }

    /**
     *
     * @return OrganizationUserPermissions| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getMyPermissions(string $orgId): OrganizationUserPermissions|\Google\Protobuf\Internal\Message
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_MY_PERMISSIONS,
            [],
            [
                'orgId' => $orgId
            ]
        );
        $permission = new OrganizationUserPermissions();
        $permission->mergeFromString($response);

        return $permission;
    }

    /**
     * @return User| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getMyUser(): User
    {
        $response = $this->doRequest(self::RESOURCE_GET_MY_USER, [], [], self::METHOD_GET);

        $user = new User();
        $user->mergeFromString($response);

        return $user;
    }

    /**
     * @return Organization| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getOrganizationById(string $orgId): Organization
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_ORGANIZATION,
            [],
            [
                'orgId' => $orgId
            ]
        );

        $org = new Organization();
        $org->mergeFromString($response);

        return $org;
    }

    /**
     * @return Organization| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getOrganizationByFnsParticipantId(string $fnsParticipantId): Organization
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_ORGANIZATION,
            [],
            [
                'fnsParticipantId' => $fnsParticipantId
            ]
        );

        $org = new Organization();
        $org->mergeFromString($response);

        return $org;
    }


    /**
     * @return Organization| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getOrganizationByInn(string $inn): Organization
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_ORGANIZATION,
            [],
            [
                'inn' => $inn
            ]
        );

        $org = new Organization();
        $org->mergeFromString($response);

        return $org;
    }

    /**
     * @param string|null $kpp
     *
     * @return OrganizationList| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getOrganizationsByInnKpp(string $inn, string $kpp = null, bool $includeRelations = false): OrganizationList
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_ORGANIZATIONS_BY_INN_KPP,
            [],
            [
                'inn' => $inn,
                'kpp' => $kpp,
                'includeRelations' => $includeRelations ? 'true' : 'false'
            ]
        );

        $org = new OrganizationList();
        $org->mergeFromString($response);

        return $org;
    }

    /**
     * @return GetOrganizationsByInnListResponse| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getOrganizationsByInnList(string $myOrgId, array $innList = []): GetOrganizationsByInnListResponse
    {
        $request = new GetOrganizationsByInnListRequest();
        $request->setInnList($innList);

        $response = $this->doRequest(
            self::RESOURCE_GET_ORGANIZATIONS_BY_INN_LIST,
            $request->serializeToString(),
            [
                'myOrgId'   => $myOrgId
            ],
            self::METHOD_POST
        );

        $data = new GetOrganizationsByInnListResponse();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     *
     * @return OrganizationUsersList| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getOrganizationUsers(string $orgId): OrganizationUsersList
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_ORGANIZATION_USERS,
            [],
            [
                'orgId' => $orgId
            ]
        );

        $data = new OrganizationUsersList();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     *
     * @return RussianAddress| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function parseRussianAddress(string $address): RussianAddress
    {
        $response = $this->doRequest(
            self::RESOURCE_PARSE_RUSSIAN_ADDRESS,
            [],
            [
                'address' => $address
            ]
        );

        $data = new RussianAddress();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     * @param string|null $comment
     *
     * @return mixed
     * @throws DiadocApiException
     */
    public function acquireCounteragent(string $myOrgId, string $counteragentOrgId, string $myDepartmentId, string $comment = null): string
    {
        return $this->doRequest(
            self::RESOURCE_ACQUIRE_COUNTERAGENTS,
            [],
            [
                'myOrgId' => $myOrgId,
                'counteragentOrgId' => $counteragentOrgId,
                'myDepartmentId'    => $myDepartmentId,
                'comment'   => $comment
            ],
            self::METHOD_POST
        );
    }

    /**
     * @param InvitationDocument|null $invitationDocument |null $invationDocument
     *
     * @return AsyncMethodResult| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function acquireCounteragentWithDocument(string $myOrgId, string $counteragentOrgId, string $myDepartmentId, InvitationDocument $invitationDocument = null, string $messageToContragent = ''): AsyncMethodResult
    {
        $request = new AcquireCounteragentRequest();
        $request->setOrgId($counteragentOrgId);
        $request->setMessageToCounteragent($messageToContragent);
        $request->setInvitationDocument($invitationDocument);

        $response = $this->doRequest(
            self::RESOURCE_ACQUIRE_COUNTERAGENTS_V2,
            $request->serializeToString(),
            [
                'myOrgId' => $myOrgId,
                'myDepartmentId'    => $myDepartmentId,
            ],
            self::METHOD_POST
        );

        $data = new AsyncMethodResult();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     * @param InvitationDocument|null $invitationDocument |null $invationDocument
     *
     * @return AsyncMethodResult| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function acquireCounteragentByInnWithDocument(string $myOrgId, string $counteragentInn, ?string $myDepartmentId = null, InvitationDocument $invitationDocument = null, string $messageToContragent = ''): AsyncMethodResult
    {
        $request = new AcquireCounteragentRequest();
        $request->setInn($counteragentInn);
        $request->setMessageToCounteragent($messageToContragent);
        $request->setInvitationDocument($invitationDocument);

        $response = $this->doRequest(
            self::RESOURCE_ACQUIRE_COUNTERAGENTS_V2,
            $request->serializeToString(),
            [
                'myOrgId' => $myOrgId,
                'myDepartmentId'    => $myDepartmentId,
            ],
            self::METHOD_POST
        );

        $data = new AsyncMethodResult();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     *
     * @return AcquireCounteragentResult| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function acquireCounteragentResult(string $taskId): AcquireCounteragentResult
    {
        $response = $this->doRequest(
            self::RESOURCE_ACQUIRE_COUNTERAGENT_RESULT,
            [],
            [
                'taskId' => $taskId
            ]
        );

        $data = new AcquireCounteragentResult();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     * @throws DiadocApiException
     */
    public function breakWithCounteragent(string $myOrgId, string $counteragentOrgId, string $comment = ''): string
    {
        return $this->doRequest(
            self::RESOURCE_BREAK_WITH_COUNTERAGENT,
            [],
            [
                'myOrgId' => $myOrgId,
                'counteragentOrgId' => $counteragentOrgId,
                'comment' => $comment
            ],
            self::METHOD_POST
        );
    }

    /**
     * @return Counteragent| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getCountragent(string $myOrgId, string $counteragentOrgId): Counteragent
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_COUNTERAGENT,
            [],
            [
                'myOrgId' => $myOrgId,
                'counteragentOrgId' => $counteragentOrgId
            ]
        );
        $data = new Counteragent();
        $data->mergeFromString($response);

        return $data;
    }


    /**
     * @return Counteragent| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getCountragentV2(string $myOrgId, string $counteragentOrgId): Counteragent
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_COUNTERAGENT_V2,
            [],
            [
                'myOrgId' => $myOrgId,
                'counteragentOrgId' => $counteragentOrgId
            ]
        );

        $data = new Counteragent();
        $data->mergeFromString($response);

        return $data;
    }


    /**
     * @param CounteragentStatus|null $counteragentStatus
     * @return CounteragentList| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getCountragents(string $myOrgId, CounteragentStatus $counteragentStatus = null, ?int $afterIndexKey = null): CounteragentList
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_COUNTERAGENTS,
            [],
            [
                'myOrgId'   => $myOrgId,
                'counteragentStatus' => $counteragentStatus,
                'afterIndexKey'  => $afterIndexKey
            ]
        );
        $data = new CounteragentList();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     * @param $myOrgId
     * @param CounteragentStatus|null $counteragentStatus
     *
     * @throws DiadocApiException
     */
    public function getCountragentsV2($myOrgId, CounteragentStatus $counteragentStatus = null, ?string $afterIndexKey = null): CounteragentList
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_COUNTERAGENTS_V2,
            [],
            [
                'myOrgId'   => $myOrgId,
                'counteragentStatus' => $counteragentStatus,
                'afterIndexKey'  => $afterIndexKey
            ]
        );
        $list = (new CounteragentList());
        $list->mergeFromString($response);

        return $list;
    }

    public function getCounteragentCertificates(string $myOrgId, string $counteragentOrgId): CounteragentCertificateList
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_COUNTERAGENT_CERTIFICATES,
            [],
            [
                'myOrgId' => $myOrgId,
                'counteragentOrgId' => $counteragentOrgId
            ]
        );

        $list = new CounteragentCertificateList();
        $list->mergeFromString($response);

        return $list;
    }

    /**
     *
     * @return mixed
     * @throws DiadocApiException
     */
    public function getEntityContent(string $boxId, string $messageId, string $entityId)
    {
        return $this->doRequest(
            self::RESOURCE_GET_ENTITY_CONTENT,
            [],
            [
                'boxId' => $boxId,
                'messageId' => $messageId,
                'entityId'  => $entityId
            ]
        );
    }

    /**
     * @param string|null $entityId
     *
     * @return Message| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getMessage(string $boxId, string $messageId, string $entityId = null, ?string $originalSignature = null): Message
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_MESSAGE,
            [],
            [
                'boxId' => $boxId,
                'messageId' => $messageId,
                'entityId'  => $entityId,
                'originalSignature' => $originalSignature
            ]
        );
        $data = new Message();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     * @return Message| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     * @throws DiadocApiUnauthorizedException
     */
    public function postMessage(MessageToPost $messageToPost, ?string $operationId = null): Message
    {
        $response = $this->doRequest(
            self::RESOURCE_POST_MESSAGE,
            $messageToPost->serializeToString(),
            [
                'operationId' => $operationId
            ],
            self::METHOD_POST
        );

        $data = new Message();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     * @return MessagePatch| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function postMessagePatch(MessagePatchToPost $messagePatchToPost, ?string $operationId = null): MessagePatch
    {
        $response = $this->doRequest(
            self::RESOURCE_POST_MESSAGE_PATCH,
            $messagePatchToPost->serializeToString(),
            [
                'operationId' => $operationId
            ],
            self::METHOD_POST
        );

        $data = new MessagePatch();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     * @param string|null $documentId
     *
     * @throws DiadocApiException
     */
    public function delete(string $boxId, string $messageId, string $documentId = null): bool
    {
        $this->doRequest(
            self::RESOURCE_DELETE,
            [],
            [
                'boxId' => $boxId,
                'messageId' => $messageId,
                'documentId' => $documentId
            ],
            self::METHOD_POST
        );

        return true;
    }

    /**
     * @throws DiadocApiException
     */
    public function forwardDocument(string $boxId, string $toBoxId, DocumentId $documentId): string
    {
        $forwardDocumentRequest = new ForwardDocumentRequest();
        $forwardDocumentRequest->setToBoxId($toBoxId);
        $forwardDocumentRequest->setDocumentId($documentId);

        return $this->doRequest(
            self::RESOURCE_FORWARD_DOCUMENT,
            $forwardDocumentRequest->serializeToString(),
            [
                'boxId' => $boxId
            ],
            self::METHOD_POST
        );
    }

    /**
     * @return Document| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getDocument(string $boxId, string $messageId, string $entityId): Document
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_DOCUMENT,
            [],
            [
                'boxId' => $boxId,
                'messageId' => $messageId,
                'entityId'  => $entityId
            ]
        );
        $data = new Document();
        $data->mergeFromString($response);

        return $data;
    }



    public function getDocuments(string $boxId, ?DocumentsFilter $documentsFilter = null, int $sortDirection = null, $afterIndexKey = null): DocumentList
    {
        if (is_null($sortDirection)) {
            $sortDirection = SortDirection::Ascending;
        }

        $params = [
            'boxId' => $boxId,
            'sortDirection' => $sortDirection,
            'afterIndexKey' => $afterIndexKey
        ];
        if (is_null($documentsFilter)) {
            $documentsFilter = DocumentsFilter::create();
        }

        $params = array_replace($params, $documentsFilter->toFilter());

        $response = $this->doRequest(
            self::RESOURCE_GET_DOCUMENTS,
            [],
            $params
        );

        $data = new DocumentList();
        $data->mergeFromString($response);

        return $data;
    }

    public function getDocumentTypes(string $boxId): GetDocumentTypesResponseV2
    {
        $params = [
            'boxId' => $boxId,
        ];

        $response = $this->doRequest(
            self::RESOURCE_GET_DOCUMENT_TYPES,
            [],
            $params
        );

        $data = new GetDocumentTypesResponseV2();
        $data->mergeFromString($response);

        return $data;
    }


    /**
     *
     * @throws DiadocApiException
     */
    public function getDocflows(string $boxId, GetDocflowBatchRequest $batchRequest): GetDocflowBatchResponse
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_DOCFLOWS,
            $batchRequest->serializeToString(),
            [
                'boxId' => $boxId
            ],
            self::METHOD_POST
        );

        $data = new GetDocflowBatchResponse();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     * @param null $afterIndexKey
     * @return GetDocflowsByPacketIdResponse| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getDocflowsByPacketId(string $boxId, string $packetId, bool $injectEntityContent = false, $afterIndexKey = null, int $count = 100): GetDocflowsByPacketIdResponse
    {
        $getDocflowsByPacketIdRequest = new GetDocflowsByPacketIdRequest();
        $getDocflowsByPacketIdRequest->setPacketId($packetId);
        $getDocflowsByPacketIdRequest->setInjectEntityContent($injectEntityContent);
        $getDocflowsByPacketIdRequest->setAfterIndexKey($afterIndexKey);
        $getDocflowsByPacketIdRequest->setCount($count);

        $response = $this->doRequest(
            self::RESOURCE_GET_DOCFLOWS_BY_PACKET_ID,
            $getDocflowsByPacketIdRequest->serializeToString(),
            [
                'boxId' => $boxId
            ],
            self::METHOD_POST
        );

        $data = new GetDocflowsByPacketIdResponse();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     * @param int|null $searchScope
     * @param null|int $firstIndex
     * @return SearchDocflowsResponse| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function searchDocflows(string $boxId, string $queryString, int $searchScope = null, int $firstIndex = null, int $count = 100): SearchDocflowsResponse
    {
        $searchDocflowRequest = new SearchDocflowsRequest();
        $searchDocflowRequest->setQueryString($queryString);
        if ($searchScope) {
            $searchDocflowRequest->setScope($searchScope);
        }

        if ($firstIndex) {
            $searchDocflowRequest->setFirstIndex($firstIndex);
        }

        $searchDocflowRequest->setCount($count);

        $response = $this->doRequest(
            self::RESOURCE_SEARCH_DOCFLOWS,
            $searchDocflowRequest->serializeToString(),
            [
                'boxId' => $boxId
            ],
            self::METHOD_POST
        );

        $data = new SearchDocflowsResponse();
        $data->mergeFromString($response);

        return $data;
    }

    /**
     * @param DateTime|null $from
     * @param DateTime|null $to
     * @throws DiadocApiException
     */
    public function getDocflowEvents(
        string $boxId,
        DateTime $from = null,
        DateTime $to = null,
        ?int $sortDirection = null,
        bool $populateDocuments = false,
        bool $populatePreviousDocumentStates = false,
        bool $injectEntityContent = false,
        ?int $afterIndexKey = null
    ): GetDocflowEventsResponse {
        $timeBasedFilter = new TimeBasedFilter();
        $fromTimestamp = null;
        $toTimestamp = null;

        if ($from !== null) {
            $fromTimestamp = new Timestamp();
            $fromTimestamp->setTicks(DateHelper::convertDateTimeToTicks($from));
        }

        if ($to !== null) {
            $toTimestamp = new Timestamp();
            $toTimestamp->setTicks(DateHelper::convertDateTimeToTicks($to));
        }

        $timeBasedFilter->setFromTimestamp($fromTimestamp);
        $timeBasedFilter->setToTimestamp($toTimestamp);
        $timeBasedFilter->setSortDirection($sortDirection);

        $getDocflowEventsRequest = new GetDocflowEventsRequest();
        $getDocflowEventsRequest->setFilter($timeBasedFilter);
        $getDocflowEventsRequest->setPopulateDocuments($populateDocuments);
        $getDocflowEventsRequest->setPopulatePreviousDocumentStates($populatePreviousDocumentStates);
        $getDocflowEventsRequest->setInjectEntityContent($injectEntityContent);
        $getDocflowEventsRequest->setAfterIndexKey($afterIndexKey);


        $response = $this->doRequest(
            self::RESOURCE_GET_DOCFLOWS_EVENTS,
            $getDocflowEventsRequest->serializeToString(),
            [
                'boxId' => $boxId
            ],
            self::METHOD_POST
        );

        $data = new GetDocflowEventsResponse();
        $data->mergeFromString($response);

        return $data;
    }


    /**
     * @throws DiadocApiException
     */
    public function getEvent(string $boxId, string $eventId): BoxEvent
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_EVENT,
            [],
            [
                'boxId' => $boxId,
                'eventId' => $eventId
            ]
        );

        $data = new BoxEvent();
        $data->mergeFromString($response);

        return $data;
    }

    public function getNewEvents(string $boxId, string $afterEventId = null): BoxEventList
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_NEW_EVENTS,
            [],
            [
                'boxId' => $boxId,
                'afterEventId' => $afterEventId
            ]
        );
        $data = new BoxEventList();
        $data->mergeFromString($response);

        return $data;
    }

    protected function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function generateInvitationDocument(string $content, string $title, bool $signatureRequested = false): InvitationDocument
    {
        $invitationDocument = new InvitationDocument();
        $invitationDocument->setFileName($title);
        $invitationDocument->setSignedContent($this->generateSignedContent($content));
        $invitationDocument->setSignatureRequested($signatureRequested);

        return $invitationDocument;
    }

    public function generateSignedContentFromFile(string $fileName): SignedContent
    {
        if (!file_exists($fileName)) {
            throw new \Exception('File not found');
        }

        $content = file_get_contents($fileName);

        return $this->generateSignedContent($content);
    }

    public function generateSignedContent(string $content): SignedContent
    {
        $signedContent = new SignedContent();
        $signedContent->setContent($content);
        $signedContent->setSignature($this->signerProvider->sign($content));

        return $signedContent;
    }


    // В документации не описано до конца как делать. Вот есть решение в issues
    // https://github.com/diadoc/diadocapi-docs/issues/323
    public function shelfUpload(string $nameOnShelf, int $partIndex, string $content, int $isLastPart): string
    {
        return $this->doRequest(
            self::RESOURCE_SHELF_UPLOAD,
            ['content' => $content],
            [
                'nameOnShelf' => $nameOnShelf,
                'partIndex' => $partIndex,
                'isLastPart'    => $isLastPart,
            ],
            self::METHOD_POST,
            self::CONTENT_FORM_URL_ENCODED
        );
    }
}
