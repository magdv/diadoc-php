<?php

namespace MagDv\Diadoc;

use Diadoc\Proto\Documents\Types\GetDocumentTypesResponseV2;
use Diadoc\Proto\Events\SignedContent;
use Diadoc\Proto\LoginPassword;
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
    final public const METHOD_GET = 'GET';

    /**
     * @var string
     */
    final public const CONTENT_FORM_URL_ENCODED = 'application/x-www-form-urlencoded';

    /**
     * @var string
     */
    final public const METHOD_POST = 'POST';

    // Authorization
    /**
     * @var string
     */
    final public const RESOURCE_AUTHENTICATE = '/Authenticate';

    /**
     * @var string
     */
    final public const RESOURCE_AUTHENTICATE_V2 = '/V2/Authenticate';

    /**
     * @var string
     */
    final public const RESOURCE_AUTHENTICATE_V3 = '/V3/Authenticate';

    /**
     * @var string
     */
    final public const RESOURCE_GET_EXTERNAL_SERVICE_AUTH_INFO = '/GetExternalServiceAuthInfo';

    // Organizations
    /**
     * @var string
     */
    final public const RESOURCE_GET_BOX = '/GetBox';

    /**
     * @var string
     */
    final public const RESOURCE_GET_DEPARTMENT = '/GetDepartment';

    /**
     * @var string
     */
    final public const RESOURCE_GET_MY_ORGANIZATION = '/GetMyOrganizations';

    /**
     * @var string
     */
    final public const RESOURCE_GET_MY_PERMISSIONS = '/GetMyPermissions';

    /**
     * @var string
     */
    final public const RESOURCE_GET_MY_USER = '/GetMyUser';

    /**
     * @var string
     */
    final public const RESOURCE_GET_ORGANIZATION = '/GetOrganization';

    /**
     * @var string
     */
    final public const RESOURCE_GET_ORGANIZATIONS_BY_INN_KPP = '/GetOrganizationsByInnKpp';

    /**
     * @var string
     */
    final public const RESOURCE_GET_ORGANIZATIONS_BY_INN_LIST = '/GetOrganizationsByInnList';

    /**
     * @var string
     */
    final public const RESOURCE_GET_ORGANIZATION_USERS = '/GetOrganizationUsers';

    /**
     * @var string
     */
    final public const RESOURCE_PARSE_RUSSIAN_ADDRESS = '/ParseRussianAddress';

    // Counteragents
    /**
     * @var string
     */
    final public const RESOURCE_ACQUIRE_COUNTERAGENTS = '/AcquireCounteragent';

    /**
     * @var string
     */
    final public const RESOURCE_ACQUIRE_COUNTERAGENTS_V2 = '/V2/AcquireCounteragent';

    /**
     * @var string
     */
    final public const RESOURCE_ACQUIRE_COUNTERAGENT_RESULT = '/AcquireCounteragentResult';

    /**
     * @var string
     */
    final public const RESOURCE_BREAK_WITH_COUNTERAGENT = '/BreakWithCounteragent';

    /**
     * @var string
     */
    final public const RESOURCE_GET_COUNTERAGENT = '/GetCounteragent';

    /**
     * @var string
     */
    final public const RESOURCE_GET_COUNTERAGENT_V2 = '/V2/GetCounteragent';

    /**
     * @var string
     */
    final public const RESOURCE_GET_COUNTERAGENTS = '/GetCounteragents';

    /**
     * @var string
     */
    final public const RESOURCE_GET_COUNTERAGENTS_V2 = '/V2/GetCounteragents';

    /**
     * @var string
     */
    final public const RESOURCE_GET_COUNTERAGENT_CERTIFICATES = '/GetCounteragentCertificates';

    // Messages
    /**
     * @var string
     */
    final public const RESOURCE_GET_ENTITY_CONTENT = ' /V4/GetEntityContent';

    /**
     * @var string
     */
    final public const RESOURCE_GET_MESSAGE = '/V3/GetMessage';

    /**
     * @var string
     */
    final public const RESOURCE_POST_MESSAGE = '/V3/PostMessage';

    /**
     * @var string
     */
    final public const RESOURCE_POST_MESSAGE_PATCH = '/V3/PostMessagePatch';

    // Documents
    /**
     * @var string
     */
    final public const RESOURCE_DELETE = '/Delete';

    /**
     * @var string
     */
    final public const RESOURCE_FORWARD_DOCUMENT = '/V2/ForwardDocument';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_ACCEPTANCE_CERTIFICATE_XML_FOR_BUYER = '/GenerateAcceptanceCertificateXmlForBuyer';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_ACCEPTANCE_CERTIFICATE_XML_FOR_SELLER = '/GenerateAcceptanceCertificateXmlForSeller';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_DOCUMENT_PROTOCOL = '/GenerateDocumentProtocol';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_DOCUMENT_ZIP = '/GenerateDocumentZip';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_FORWARDED_DOCUMENT_PROTOCOL = '/V2/GenerateForwardedDocumentProtocol';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_PRINT_FORM = '/GeneratePrintForm';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_PRINT_FORM_FROM_ATTACHMENT = '/GeneratePrintFormFromAttachment';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_REVOCATION_REQUEST_XML = '/GenerateRevocationRequestXml';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_SIGNATURE_REJECTION_XML = '/GenerateSignatureRejectionXml';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_TORG_12_XML_FOR_BUYER = '/GenerateTorg12XmlForBuyer';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_TORG_12_XML_FOR_SELLER = '/GenerateTorg12XmlForSeller';

    /**
     * @var string
     */
    final public const RESOURCE_GET_DOCUMENT = '/V3/GetDocument';

    /**
     * @var string
     */
    final public const RESOURCE_GET_DOCUMENTS = '/V3/GetDocuments';

    /**
     * @var string
     */
    final public const RESOURCE_GET_DOCUMENT_TYPES = '/V2/GetDocumentTypes';

    /**
     * @var string
     */
    final public const RESOURCE_GET_FORWARDED_DOCUMENT_EVENTS = '/V2/GetForwardedDocumentEvents';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_FORWARDED_DOCUMENT_PRINT_FORM = '/GenerateForwardedDocumentPrintForm';

    /**
     * @var string
     */
    final public const RESOURCE_GET_FORWARDED_ENTITY_CONTENT = '/V2/GetForwardedEntityContent';

    /**
     * @var string
     */
    final public const RESOURCE_GET_FORWARDED_DOCUMENT = '/V2/GetForwardedDocuments';

    /**
     * @var string
     */
    final public const RESOURCE_GET_GENERATED_PRINT_FORM = '/GetGeneratedPrintForm';

    /**
     * @var string
     */
    final public const RESOURCE_GET_RECOGNIZED = '/GetRecognized';

    /**
     * @var string
     */
    final public const RESOURCE_MOVE_DOCUMENTS = '/MoveDocuments';

    /**
     * @var string
     */
    final public const RESOURCE_PARSE_ACCEPTANCE_CERTIFICATE_SELLER_TITLE_XML = '/ParseAcceptanceCertificateSellerTitleXml';

    /**
     * @var string
     */
    final public const RESOURCE_PARSE_REVOCATION_REQUEST_XML = '/ParseRevocationRequestXml';

    /**
     * @var string
     */
    final public const RESOURCE_PARSE_SIGNATURE_REJECTION_XML = '/ParseSignatureRejectionXml';

    /**
     * @var string
     */
    final public const RESOURCE_PARSE_TORG_12_SELLER_TITLE_XML = '/ParseTorg12SellerTitleXml';

    /**
     * @var string
     */
    final public const RESOURCE_PREPARE_DOCUMENTS_TO_SIGN = '/PrepareDocumentsToSign';

    /**
     * @var string
     */
    final public const RESOURCE_RECOGNIZE = '/Recognize';

    /**
     * @var string
     */
    final public const RESOURCE_RECYCLE_DRAFT = '/RecycleDraft';

    /**
     * @var string
     */
    final public const RESOURCE_RESTORE = '/Restore';

    /**
     * @var string
     */
    final public const RESOURCE_SHELF_UPLOAD = '/ShelfUpload';

    /**
     * @var string
     */
    final public const RESOURCE_SEND_DRAFT = '/SendDraft';

    // SF/ISF/KSF
    /**
     * @var string
     */
    final public const RESOURCE_CAN_SEND_INVOICE = '/CanSendInvoice';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_INVOICE_XML = '/GenerateInvoiceXml';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_INVOICE_CORRECTION_REQUEST_XML = '/GenerateInvoiceCorrectionRequestXml';

    /**
     * @var string
     */
    final public const RESOURCE_GENERATE_INVOICE_DOCUMENT_RECEIPT_XML = '/GenerateInvoiceDocumentReceiptXml';

    /**
     * @var string
     */
    final public const RESOURCE_GET_INVOICE_CORRECTION_REQUEST_INFO = '/GetInvoiceCorrectionRequestInfo';

    /**
     * @var string
     */
    final public const RESOURCE_PARSE_INVOICE_XML = '/ParseInvoiceXml';

    // Events
    /**
     * @var string
     */
    final public const RESOURCE_GET_EVENT = '/V2/GetEvent';

    /**
     * @var string
     */
    final public const RESOURCE_GET_NEW_EVENTS = '/V4/GetNewEvents';

    //Docflow API
    /**
     * @var string
     */
    final public const RESOURCE_GET_DOCFLOWS = '/V2/GetDocflows';

    /**
     * @var string
     */
    final public const RESOURCE_GET_DOCFLOWS_BY_PACKET_ID = '/V2/GetDocflowsByPacketId';

    /**
     * @var string
     */
    final public const RESOURCE_SEARCH_DOCFLOWS = '/V2/SearchDocflows';

    /**
     * @var string
     */
    final public const RESOURCE_GET_DOCFLOWS_EVENTS = '/V2/GetDocflowEvents';

    // Cloud sign
    /**
     * @var string
     */
    final public const RESOURCE_CLOUD_SIGN = '/CloudSign';

    /**
     * @var string
     */
    final public const RESOURCE_CLOUD_SIGN_CONFIRM = '/CloudSignConfirm';

    /**
     * @var string
     */
    final public const RESOURCE_CLOUD_SIGN_CONFIRM_RESULT = '/CloudSignConfirmResult';

    /**
     * @var string
     */
    final public const RESOURCE_CLOUD_SIGN_RESULT = '/CloudSignResult';

    /**
     * @var string
     */
    final public const RESOURCE_AUTO_SIGN_RECEIPTS = '/AutoSignReceipts';

    /**
     * @var string
     */
    final public const RESOURCE_AUTO_SIGN_RECEIPTS_RESULT = '/AutoSignReceiptsResult';

    private ?string $token = null;

    public function __construct(
        private readonly string $ddauthApiClientId,
        private readonly string $serviceUrl = 'https://diadoc-api.kontur.ru/',
        private readonly bool $debugRequest = false,
        private readonly ?SignerProviderInterface $signerProvider = null
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

    public function authenticateLoginV3(string $login, string $password): string
    {
        $loginPassword = new LoginPassword();
        $loginPassword->setLogin($login);
        $loginPassword->setPassword($password);

        $response = $this->doRequest(
            self::RESOURCE_AUTHENTICATE_V3,
            $loginPassword->serializeToString(),
            [
                'type' => 'password'
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
        if (!$this->getToken() && !in_array($resource, [self::RESOURCE_AUTHENTICATE, self::RESOURCE_AUTHENTICATE_V2, self::RESOURCE_AUTHENTICATE_V3], true)) {
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

        if ($this->debugRequest) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_STDERR, STDOUT);
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

        $organizationList = new OrganizationList();
        $organizationList->mergeFromString($response);

        return $organizationList;
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
        $organizationUserPermissions = new OrganizationUserPermissions();
        $organizationUserPermissions->mergeFromString($response);

        return $organizationUserPermissions;
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

        $organization = new Organization();
        $organization->mergeFromString($response);

        return $organization;
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

        $organization = new Organization();
        $organization->mergeFromString($response);

        return $organization;
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

        $organization = new Organization();
        $organization->mergeFromString($response);

        return $organization;
    }

    /**
     * @param string|null $kpp
     *
     * @return OrganizationList| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getOrganizationsByInnKpp(string $inn, ?string $kpp = null, bool $includeRelations = false): OrganizationList
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

        $organizationList = new OrganizationList();
        $organizationList->mergeFromString($response);

        return $organizationList;
    }

    /**
     * @return GetOrganizationsByInnListResponse| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function getOrganizationsByInnList(string $myOrgId, array $innList = []): GetOrganizationsByInnListResponse
    {
        $getOrganizationsByInnListRequest = new GetOrganizationsByInnListRequest();
        $getOrganizationsByInnListRequest->setInnList($innList);

        $response = $this->doRequest(
            self::RESOURCE_GET_ORGANIZATIONS_BY_INN_LIST,
            $getOrganizationsByInnListRequest->serializeToString(),
            [
                'myOrgId'   => $myOrgId
            ],
            self::METHOD_POST
        );

        $getOrganizationsByInnListResponse = new GetOrganizationsByInnListResponse();
        $getOrganizationsByInnListResponse->mergeFromString($response);

        return $getOrganizationsByInnListResponse;
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

        $organizationUsersList = new OrganizationUsersList();
        $organizationUsersList->mergeFromString($response);

        return $organizationUsersList;
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

        $russianAddress = new RussianAddress();
        $russianAddress->mergeFromString($response);

        return $russianAddress;
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
        $acquireCounteragentRequest = new AcquireCounteragentRequest();
        $acquireCounteragentRequest->setOrgId($counteragentOrgId);
        $acquireCounteragentRequest->setMessageToCounteragent($messageToContragent);
        $acquireCounteragentRequest->setInvitationDocument($invitationDocument);

        $response = $this->doRequest(
            self::RESOURCE_ACQUIRE_COUNTERAGENTS_V2,
            $acquireCounteragentRequest->serializeToString(),
            [
                'myOrgId' => $myOrgId,
                'myDepartmentId'    => $myDepartmentId,
            ],
            self::METHOD_POST
        );

        $asyncMethodResult = new AsyncMethodResult();
        $asyncMethodResult->mergeFromString($response);

        return $asyncMethodResult;
    }

    /**
     * @param InvitationDocument|null $invitationDocument |null $invationDocument
     *
     * @return AsyncMethodResult| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function acquireCounteragentByInnWithDocument(string $myOrgId, string $counteragentInn, ?string $myDepartmentId = null, InvitationDocument $invitationDocument = null, string $messageToContragent = ''): AsyncMethodResult
    {
        $acquireCounteragentRequest = new AcquireCounteragentRequest();
        $acquireCounteragentRequest->setInn($counteragentInn);
        $acquireCounteragentRequest->setMessageToCounteragent($messageToContragent);
        $acquireCounteragentRequest->setInvitationDocument($invitationDocument);

        $response = $this->doRequest(
            self::RESOURCE_ACQUIRE_COUNTERAGENTS_V2,
            $acquireCounteragentRequest->serializeToString(),
            [
                'myOrgId' => $myOrgId,
                'myDepartmentId'    => $myDepartmentId,
            ],
            self::METHOD_POST
        );

        $asyncMethodResult = new AsyncMethodResult();
        $asyncMethodResult->mergeFromString($response);

        return $asyncMethodResult;
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

        $acquireCounteragentResult = new AcquireCounteragentResult();
        $acquireCounteragentResult->mergeFromString($response);

        return $acquireCounteragentResult;
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
        $counteragent = new Counteragent();
        $counteragent->mergeFromString($response);

        return $counteragent;
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

        $counteragent = new Counteragent();
        $counteragent->mergeFromString($response);

        return $counteragent;
    }


    /**
     * @param string $myOrgId
     * @param string|null $counteragentStatus
     * @param int|null $afterIndexKey
     * @return \Diadoc\Proto\CounteragentList
     * @throws \MagDv\Diadoc\Exception\DiadocApiException
     * @throws \MagDv\Diadoc\Exception\DiadocApiUnauthorizedException
     */
    public function getCountragents(string $myOrgId, string $counteragentStatus = null, ?int $afterIndexKey = null): CounteragentList
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
        $counteragentList = new CounteragentList();
        $counteragentList->mergeFromString($response);

        return $counteragentList;
    }

    public function getCountragentsV2(string $myOrgId, string $counteragentStatus = null, ?string $afterIndexKey = null, ?string $query = null): CounteragentList
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_COUNTERAGENTS_V2,
            [],
            [
                'myOrgId'   => $myOrgId,
                'counteragentStatus' => $counteragentStatus,
                'afterIndexKey'  => $afterIndexKey,
                'query'  => $query,
            ]
        );
        $counteragentList = (new CounteragentList());
        $counteragentList->mergeFromString($response);

        return $counteragentList;
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

        $counteragentCertificateList = new CounteragentCertificateList();
        $counteragentCertificateList->mergeFromString($response);

        return $counteragentCertificateList;
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
        $message = new Message();
        $message->mergeFromString($response);

        return $message;
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

        $message = new Message();
        $message->mergeFromString($response);

        return $message;
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

        $messagePatch = new MessagePatch();
        $messagePatch->mergeFromString($response);

        return $messagePatch;
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
    public function getDocument(string $boxId, string $messageId, string $entityId, string $injectEntityContent = 'true'): Document
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_DOCUMENT,
            [],
            [
                'boxId' => $boxId,
                'messageId' => $messageId,
                'entityId'  => $entityId,
                'injectEntityContent'  => $injectEntityContent
            ]
        );
        $document = new Document();
        $document->mergeFromString($response);

        return $document;
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

        $documentList = new DocumentList();
        $documentList->mergeFromString($response);

        return $documentList;
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

        $getDocumentTypesResponseV2 = new GetDocumentTypesResponseV2();
        $getDocumentTypesResponseV2->mergeFromString($response);

        return $getDocumentTypesResponseV2;
    }


    /**
     *
     * @throws DiadocApiException
     */
    public function getDocflows(string $boxId, GetDocflowBatchRequest $getDocflowBatchRequest): GetDocflowBatchResponse
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_DOCFLOWS,
            $getDocflowBatchRequest->serializeToString(),
            [
                'boxId' => $boxId
            ],
            self::METHOD_POST
        );

        $getDocflowBatchResponse = new GetDocflowBatchResponse();
        $getDocflowBatchResponse->mergeFromString($response);

        return $getDocflowBatchResponse;
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

        $getDocflowsByPacketIdResponse = new GetDocflowsByPacketIdResponse();
        $getDocflowsByPacketIdResponse->mergeFromString($response);

        return $getDocflowsByPacketIdResponse;
    }

    /**
     * @param int|null $searchScope
     * @param null|int $firstIndex
     * @return SearchDocflowsResponse| \Google\Protobuf\Internal\Message
     * @throws DiadocApiException
     */
    public function searchDocflows(string $boxId, string $queryString, int $searchScope = null, int $firstIndex = null, int $count = 100): SearchDocflowsResponse
    {
        $searchDocflowsRequest = new SearchDocflowsRequest();
        $searchDocflowsRequest->setQueryString($queryString);
        if ($searchScope) {
            $searchDocflowsRequest->setScope($searchScope);
        }

        if ($firstIndex) {
            $searchDocflowsRequest->setFirstIndex($firstIndex);
        }

        $searchDocflowsRequest->setCount($count);

        $response = $this->doRequest(
            self::RESOURCE_SEARCH_DOCFLOWS,
            $searchDocflowsRequest->serializeToString(),
            [
                'boxId' => $boxId
            ],
            self::METHOD_POST
        );

        $searchDocflowsResponse = new SearchDocflowsResponse();
        $searchDocflowsResponse->mergeFromString($response);

        return $searchDocflowsResponse;
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

        $getDocflowEventsResponse = new GetDocflowEventsResponse();
        $getDocflowEventsResponse->mergeFromString($response);

        return $getDocflowEventsResponse;
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

        $boxEvent = new BoxEvent();
        $boxEvent->mergeFromString($response);

        return $boxEvent;
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
        $boxEventList = new BoxEventList();
        $boxEventList->mergeFromString($response);

        return $boxEventList;
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
