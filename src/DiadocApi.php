<?php

namespace MagDv\Diadoc;

use DateTime;
use Diadoc\Proto\AcquireCounteragentRequest;
use Diadoc\Proto\AcquireCounteragentResult;
use Diadoc\Proto\AsyncMethodResult;
use Diadoc\Proto\Box;
use Diadoc\Proto\Counteragent;
use Diadoc\Proto\CounteragentCertificateList;
use Diadoc\Proto\CounteragentList;
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
use Diadoc\Proto\Documents\Types\GetDocumentTypesResponseV2;
use Diadoc\Proto\Events\BoxEvent;
use Diadoc\Proto\Events\BoxEventList;
use Diadoc\Proto\Events\Message;
use Diadoc\Proto\Events\MessagePatch;
use Diadoc\Proto\Events\MessagePatchToPost;
use Diadoc\Proto\Events\MessageToPost;
use Diadoc\Proto\Events\SignedContent;
use Diadoc\Proto\Forwarding\ForwardDocumentRequest;
use Diadoc\Proto\GetDocflowBatchResponseV5;
use Diadoc\Proto\GetOrganizationsByInnListRequest;
use Diadoc\Proto\GetOrganizationsByInnListResponse;
use Diadoc\Proto\InvitationDocument;
use Diadoc\Proto\LoginPassword;
use Diadoc\Proto\Organization;
use Diadoc\Proto\OrganizationList;
use Diadoc\Proto\OrganizationUserPermissions;
use Diadoc\Proto\OrganizationUsersList;
use Diadoc\Proto\RussianAddress;
use Diadoc\Proto\SortDirection;
use Diadoc\Proto\TimeBasedFilter;
use Diadoc\Proto\Timestamp;
use Diadoc\Proto\User;
use MagDv\Diadoc\Auth\AuthenticateV3AuthMode;
use MagDv\Diadoc\Auth\AuthModeInterface;
use MagDv\Diadoc\Auth\OidcAuthFlowAuthMode;
use MagDv\Diadoc\Entities\Http\HttpLogDto;
use MagDv\Diadoc\Exception\DiadocApiException;
use MagDv\Diadoc\Exception\DiadocApiUnauthorizedException;
use MagDv\Diadoc\Filter\DocumentsFilter;
use MagDv\Diadoc\Helper\DateHelper;
use MagDv\Diadoc\Interfaces\HttpLoggerInterface;
use MagDv\Diadoc\Signer\Interfaces\SignerProviderInterface;

class DiadocApi
{
    public const string METHOD_GET = 'GET';

    public const string CONTENT_FORM_URL_ENCODED = 'application/x-www-form-urlencoded';

    public const string METHOD_POST = 'POST';

    /**
     * Режим авторизации OpenID Connect (Authorization: Bearer).
     */
    public const string AUTH_MODE_OIDC = OidcAuthFlowAuthMode::MODE;

    /**
     * Устаревший режим авторизации DiadocAuth (ddauth_api_client_id + ddauth_token).
     */
    public const string AUTH_MODE_AUTHENTICATE_V3 = AuthenticateV3AuthMode::MODE;

    // Authorization
    public const string RESOURCE_AUTHENTICATE = '/Authenticate';

    public const string RESOURCE_AUTHENTICATE_V2 = '/V2/Authenticate';

    public const string RESOURCE_AUTHENTICATE_V3 = '/V3/Authenticate';

    public const string RESOURCE_GET_EXTERNAL_SERVICE_AUTH_INFO = '/GetExternalServiceAuthInfo';

    // Organizations
    public const string RESOURCE_GET_BOX = '/GetBox';

    public const string RESOURCE_GET_DEPARTMENT = '/GetDepartment';

    public const string RESOURCE_GET_MY_ORGANIZATION = '/GetMyOrganizations';

    public const string RESOURCE_GET_MY_PERMISSIONS = '/GetMyPermissions';

    public const string RESOURCE_GET_MY_USER = '/GetMyUser';

    public const string RESOURCE_GET_ORGANIZATION = '/GetOrganization';

    public const string RESOURCE_GET_ORGANIZATIONS_BY_INN_KPP = '/GetOrganizationsByInnKpp';

    public const string RESOURCE_GET_ORGANIZATIONS_BY_INN_LIST = '/GetOrganizationsByInnList';

    public const string RESOURCE_GET_ORGANIZATION_USERS = '/GetOrganizationUsers';

    public const string RESOURCE_PARSE_RUSSIAN_ADDRESS = '/ParseRussianAddress';

    // Counteragents
    public const string RESOURCE_ACQUIRE_COUNTERAGENTS = '/AcquireCounteragent';

    public const string RESOURCE_ACQUIRE_COUNTERAGENTS_V2 = '/V2/AcquireCounteragent';

    public const string RESOURCE_ACQUIRE_COUNTERAGENT_RESULT = '/AcquireCounteragentResult';

    public const string RESOURCE_BREAK_WITH_COUNTERAGENT = '/BreakWithCounteragent';

    public const string RESOURCE_GET_COUNTERAGENT = '/GetCounteragent';

    public const string RESOURCE_GET_COUNTERAGENT_V2 = '/V2/GetCounteragent';

    public const string RESOURCE_GET_COUNTERAGENTS = '/GetCounteragents';

    public const string RESOURCE_GET_COUNTERAGENTS_V2 = '/V2/GetCounteragents';

    public const string RESOURCE_GET_COUNTERAGENT_CERTIFICATES = '/GetCounteragentCertificates';

    // Messages
    public const string RESOURCE_GET_ENTITY_CONTENT = '/V4/GetEntityContent';

    public const string RESOURCE_GET_MESSAGE = '/V3/GetMessage';

    public const string GET_MESSAGE_V6 = '/V6/GetMessage';

    public const string RESOURCE_POST_MESSAGE = '/V3/PostMessage';

    public const string RESOURCE_POST_MESSAGE_PATCH = '/V3/PostMessagePatch';

    // Documents
    public const string RESOURCE_DELETE = '/Delete';

    public const string RESOURCE_FORWARD_DOCUMENT = '/V2/ForwardDocument';

    public const string RESOURCE_GENERATE_ACCEPTANCE_CERTIFICATE_XML_FOR_BUYER = '/GenerateAcceptanceCertificateXmlForBuyer';

    public const string RESOURCE_GENERATE_ACCEPTANCE_CERTIFICATE_XML_FOR_SELLER = '/GenerateAcceptanceCertificateXmlForSeller';

    public const string RESOURCE_GENERATE_DOCUMENT_PROTOCOL = '/GenerateDocumentProtocol';

    public const string RESOURCE_GENERATE_DOCUMENT_ZIP = '/GenerateDocumentZip';

    public const string RESOURCE_GENERATE_FORWARDED_DOCUMENT_PROTOCOL = '/V2/GenerateForwardedDocumentProtocol';

    public const string RESOURCE_GENERATE_PRINT_FORM = '/GeneratePrintForm';

    public const string RESOURCE_GENERATE_PRINT_FORM_FROM_ATTACHMENT = '/GeneratePrintFormFromAttachment';

    public const string RESOURCE_GENERATE_REVOCATION_REQUEST_XML = '/GenerateRevocationRequestXml';

    public const string RESOURCE_GENERATE_SIGNATURE_REJECTION_XML = '/GenerateSignatureRejectionXml';

    public const string RESOURCE_GENERATE_TORG_12_XML_FOR_BUYER = '/GenerateTorg12XmlForBuyer';

    public const string RESOURCE_GENERATE_TORG_12_XML_FOR_SELLER = '/GenerateTorg12XmlForSeller';

    public const string RESOURCE_GET_DOCUMENT = '/V3/GetDocument';

    public const string RESOURCE_GET_DOCUMENTS = '/V3/GetDocuments';

    public const string RESOURCE_GET_DOCUMENT_TYPES = '/V2/GetDocumentTypes';

    public const string RESOURCE_GET_FORWARDED_DOCUMENT_EVENTS = '/V2/GetForwardedDocumentEvents';

    public const string RESOURCE_GENERATE_FORWARDED_DOCUMENT_PRINT_FORM = '/GenerateForwardedDocumentPrintForm';

    public const string RESOURCE_GET_FORWARDED_ENTITY_CONTENT = '/V2/GetForwardedEntityContent';

    public const string RESOURCE_GET_FORWARDED_DOCUMENT = '/V2/GetForwardedDocuments';

    public const string RESOURCE_GET_GENERATED_PRINT_FORM = '/GetGeneratedPrintForm';

    public const string RESOURCE_GET_RECOGNIZED = '/GetRecognized';

    public const string RESOURCE_MOVE_DOCUMENTS = '/MoveDocuments';

    public const string RESOURCE_PARSE_ACCEPTANCE_CERTIFICATE_SELLER_TITLE_XML = '/ParseAcceptanceCertificateSellerTitleXml';

    public const string RESOURCE_PARSE_REVOCATION_REQUEST_XML = '/ParseRevocationRequestXml';

    public const string RESOURCE_PARSE_SIGNATURE_REJECTION_XML = '/ParseSignatureRejectionXml';

    public const string RESOURCE_PARSE_TORG_12_SELLER_TITLE_XML = '/ParseTorg12SellerTitleXml';

    public const string RESOURCE_PREPARE_DOCUMENTS_TO_SIGN = '/PrepareDocumentsToSign';

    public const string RESOURCE_RECOGNIZE = '/Recognize';

    public const string RESOURCE_RECYCLE_DRAFT = '/RecycleDraft';

    public const string RESOURCE_RESTORE = '/Restore';

    public const string RESOURCE_SHELF_UPLOAD = '/ShelfUpload';

    public const string RESOURCE_SEND_DRAFT = '/SendDraft';

    // SF/ISF/KSF
    public const string RESOURCE_CAN_SEND_INVOICE = '/CanSendInvoice';

    public const string RESOURCE_GENERATE_INVOICE_XML = '/GenerateInvoiceXml';

    public const string RESOURCE_GENERATE_INVOICE_CORRECTION_REQUEST_XML = '/GenerateInvoiceCorrectionRequestXml';

    public const string RESOURCE_GENERATE_INVOICE_DOCUMENT_RECEIPT_XML = '/GenerateInvoiceDocumentReceiptXml';

    public const string RESOURCE_GET_INVOICE_CORRECTION_REQUEST_INFO = '/GetInvoiceCorrectionRequestInfo';

    public const string RESOURCE_PARSE_INVOICE_XML = '/ParseInvoiceXml';

    // Events
    public const string RESOURCE_GET_EVENT = '/V2/GetEvent';

    public const string RESOURCE_GET_NEW_EVENTS = '/V4/GetNewEvents';

    public const string RESOURCE_GET_NEW_EVENTS_V8 = '/V8/GetNewEvents';

    // Docflow API
    public const string RESOURCE_GET_DOCFLOWS = '/V2/GetDocflows';

    // Docflow API
    public const string GET_DOCFLOWS_V5 = '/V5/GetDocflows';

    public const string RESOURCE_GET_DOCFLOWS_BY_PACKET_ID = '/V2/GetDocflowsByPacketId';

    public const string RESOURCE_SEARCH_DOCFLOWS = '/V2/SearchDocflows';

    public const string RESOURCE_GET_DOCFLOWS_EVENTS = '/V2/GetDocflowEvents';

    // Cloud sign
    public const string RESOURCE_CLOUD_SIGN = '/CloudSign';

    public const string RESOURCE_CLOUD_SIGN_CONFIRM = '/CloudSignConfirm';

    public const string RESOURCE_CLOUD_SIGN_CONFIRM_RESULT = '/CloudSignConfirmResult';

    public const string RESOURCE_CLOUD_SIGN_RESULT = '/CloudSignResult';

    public const string RESOURCE_AUTO_SIGN_RECEIPTS = '/AutoSignReceipts';

    public const string RESOURCE_AUTO_SIGN_RECEIPTS_RESULT = '/AutoSignReceiptsResult';

    private AuthModeInterface $authMode;

    private ?HttpLoggerInterface $httpLogger = null;

    /**
     * Конструктор для обратной совместимости: использует устаревший DiadocAuth (authenticate_v3).
     *
     * Для произвольного режима авторизации (в т.ч. OIDC) используйте {@see self::create()}.
     */
    public function __construct(
        string $ddauthApiClientId,
        private readonly string $serviceUrl = 'https://diadoc-api.kontur.ru/',
        private readonly bool $debugRequest = false,
        private readonly ?SignerProviderInterface $signerProvider = null
    ) {
        $this->authMode = new AuthenticateV3AuthMode($ddauthApiClientId);
    }

    /**
     * Создать экземпляр API с произвольным режимом авторизации.
     *
     * Пример с OpenID Connect:
     * <code>
     * $api = DiadocApi::create(
     *     new OidcAuthFlowAuthMode($clientId, $clientSecret, 'https://identity.kontur.ru', $serviceUrl),
     *     $serviceUrl
     * );
     * </code>
     *
     * @param AuthModeInterface            $authMode       стратегия авторизации ({@see OidcAuthFlowAuthMode}, {@see AuthenticateV3AuthMode}, ...)
     * @param string                       $serviceUrl     базовый URL API Диадока
     * @param bool                         $debugRequest   логировать HTTP-запросы
     * @param SignerProviderInterface|null $signerProvider провайдер подписи для cloud-подписания
     *
     * @throws \RuntimeException если serviceUrl стратегии не совпадает с $serviceUrl
     */
    public static function create(
        AuthModeInterface $authMode,
        string $serviceUrl = 'https://diadoc-api.kontur.ru/',
        bool $debugRequest = false,
        ?SignerProviderInterface $signerProvider = null
    ): self {
        // OidcAuthFlowAuthMode резолвит scope по своему serviceUrl — он обязан совпадать
        // с URL, по которому будет ходить сам клиент. Проверка здесь гарантирует,
        // что рассинхронизация конфигурации будет обнаружена сразу при создании клиента.
        if ($authMode instanceof OidcAuthFlowAuthMode
            && \rtrim($authMode->serviceUrl, '/') !== \rtrim($serviceUrl, '/')
        ) {
            throw new \RuntimeException(
                \sprintf(
                    'OidcAuthFlowAuthMode создан для "%s", а DiadocApi будет работать с "%s": scope может быть определён неверно',
                    $authMode->serviceUrl,
                    $serviceUrl
                )
            );
        }

        $api = new self('', $serviceUrl, $debugRequest, $signerProvider);
        $api->authMode = $authMode;

        return $api;
    }

    public function authenticateLogin(string $login, string $password): string
    {
        $response = $this->doRequest(
            self::RESOURCE_AUTHENTICATE,
            [],
            [
                'login' => $login,
                'password' => $password,
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
                'type' => 'password',
            ],
            self::METHOD_POST
        );

        $this->setToken($response);

        return $response;
    }

    private function buildRequestHeaders(?string $contentType = null, string $method = self::METHOD_GET, bool $forAuthenticateCall = false): array
    {
        return $this->authMode->buildRequestHeaders($contentType, $method, $forAuthenticateCall);
    }

    /**
     * @throws DiadocApiException
     * @throws DiadocApiUnauthorizedException
     * @param array<array<string, mixed>, mixed> $queryParams
     */
    private function doRequest(string $resource, array|string $postData = [], array $queryParams = [], string $method = self::METHOD_GET, ?string $contentType = null): string
    {
        $isAuthenticateEndpoint = \in_array($resource, [self::RESOURCE_AUTHENTICATE, self::RESOURCE_AUTHENTICATE_V2, self::RESOURCE_AUTHENTICATE_V3], true);

        if (! $isAuthenticateEndpoint) {
            $this->authMode->ensureAuthorized();
        }

        $uri = \sprintf(
            '%s%s?%s',
            $this->serviceUrl,
            $resource,
            \http_build_query($queryParams)
        );

        $attempt = 0;
        while (true) {
            try {
                return $this->executeDiadocHttpRequest($uri, $postData, $method, $contentType, $isAuthenticateEndpoint);
            } catch (DiadocApiUnauthorizedException $e) {
                ++$attempt;
                if ($attempt > 1 || ! $this->authMode->handleUnauthorized()) {
                    throw $e;
                }
            }
        }
    }

    /**
     * @param array|string $postData
     *
     * @throws DiadocApiException
     * @throws DiadocApiUnauthorizedException
     */
    private function executeDiadocHttpRequest(string $uri, mixed $postData, string $method, ?string $contentType, bool $forAuthenticateCall = false): string
    {
        $ch = \curl_init($uri);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        \curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildRequestHeaders($contentType, $method, $forAuthenticateCall));

        if ($method === self::METHOD_POST) {
            \curl_setopt($ch, CURLOPT_POST, 0);
            \curl_setopt($ch, CURLOPT_POSTFIELDS, \is_array($postData) ? \http_build_query($postData) : $postData);
        } elseif ($method === self::METHOD_GET) {
            \curl_setopt($ch, CURLOPT_HTTPGET, 1);
            \curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        }

        if ($this->debugRequest) {
            \curl_setopt($ch, CURLOPT_VERBOSE, true);
            \curl_setopt($ch, CURLOPT_STDERR, STDOUT);
        }

        $response = \curl_exec($ch);

        if (\curl_errno($ch) !== 0) {
            throw new DiadocApiException(\sprintf('Curl error: (%s) %s', \curl_errno($ch), \curl_error($ch)), \curl_errno($ch));
        }

        $httpCode = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->logRequest($uri, $postData, $method, $httpCode, (string) $response);

        if (! $httpCode || ($httpCode !== 200 && $httpCode !== 204)) {
            $message = \sprintf('Curl error http code: (%s) %s', $httpCode, (string) $response);
            if ($httpCode === 401) {
                throw new DiadocApiUnauthorizedException($message, $httpCode);
            }

            throw new DiadocApiException($message, $httpCode);
        }

        \curl_close($ch);

        if ($response === false) {
            throw new DiadocApiException('Diadoc request error false returned');
        }

        return $response;
    }

    /**
     * Передать данные запроса/ответа в логгер (если он задан).
     *
     * @param array|string $postData
     */
    protected function logRequest(string $uri, mixed $postData, string $method, int $httpCode, string $response): void
    {
        if (!$this->httpLogger instanceof HttpLoggerInterface) {
            return;
        }

        $params = null;
        if (\is_array($postData) && $postData !== []) {
            $params = \http_build_query($postData);
        } elseif (\is_string($postData) && $postData !== '') {
            $params = $postData;
        }

        $this->httpLogger->log(
            new HttpLogDto(
                url: $uri,
                method: $method,
                response: $response,
                statusCode: $httpCode,
                params: $params
            )
        );
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
                'boxId' => $boxId,
            ]
        );

        $box = new Box();
        $box->mergeFromString($response);

        return $box;
    }

    /**
     * @throws DiadocApiException
     */
    public function getDepartment(string $orgId, string $departmentId): Department|\Google\Protobuf\Internal\Message
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_DEPARTMENT,
            [],
            [
                'orgId' => $orgId,
                'departmentId' => $departmentId,
            ]
        );

        $department = new Department();
        $department->mergeFromString($response);

        return $department;
    }

    /**
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
     * @throws DiadocApiException
     */
    public function getMyPermissions(string $orgId): OrganizationUserPermissions|\Google\Protobuf\Internal\Message
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_MY_PERMISSIONS,
            [],
            [
                'orgId' => $orgId,
            ]
        );
        $organizationUserPermissions = new OrganizationUserPermissions();
        $organizationUserPermissions->mergeFromString($response);

        return $organizationUserPermissions;
    }

    /**
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
     * @throws DiadocApiException
     */
    public function getOrganizationById(string $orgId): Organization
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_ORGANIZATION,
            [],
            [
                'orgId' => $orgId,
            ]
        );

        $organization = new Organization();
        $organization->mergeFromString($response);

        return $organization;
    }

    /**
     * @throws DiadocApiException
     */
    public function getOrganizationByFnsParticipantId(string $fnsParticipantId): Organization
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_ORGANIZATION,
            [],
            [
                'fnsParticipantId' => $fnsParticipantId,
            ]
        );

        $organization = new Organization();
        $organization->mergeFromString($response);

        return $organization;
    }

    /**
     * @throws DiadocApiException
     */
    public function getOrganizationByInn(string $inn): Organization
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_ORGANIZATION,
            [],
            [
                'inn' => $inn,
            ]
        );

        $organization = new Organization();
        $organization->mergeFromString($response);

        return $organization;
    }

    /**
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
                'includeRelations' => $includeRelations ? 'true' : 'false',
            ]
        );

        $organizationList = new OrganizationList();
        $organizationList->mergeFromString($response);

        return $organizationList;
    }

    /**
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
                'myOrgId' => $myOrgId,
            ],
            self::METHOD_POST
        );

        $getOrganizationsByInnListResponse = new GetOrganizationsByInnListResponse();
        $getOrganizationsByInnListResponse->mergeFromString($response);

        return $getOrganizationsByInnListResponse;
    }

    /**
     * @throws DiadocApiException
     */
    public function getOrganizationUsers(string $orgId): OrganizationUsersList
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_ORGANIZATION_USERS,
            [],
            [
                'orgId' => $orgId,
            ]
        );

        $organizationUsersList = new OrganizationUsersList();
        $organizationUsersList->mergeFromString($response);

        return $organizationUsersList;
    }

    /**
     * @throws DiadocApiException
     */
    public function parseRussianAddress(string $address): RussianAddress
    {
        $response = $this->doRequest(
            self::RESOURCE_PARSE_RUSSIAN_ADDRESS,
            [],
            [
                'address' => $address,
            ]
        );

        $russianAddress = new RussianAddress();
        $russianAddress->mergeFromString($response);

        return $russianAddress;
    }

    /**
     * @throws DiadocApiException
     */
    public function acquireCounteragent(string $myOrgId, string $counteragentOrgId, string $myDepartmentId, ?string $comment = null): string
    {
        return $this->doRequest(
            self::RESOURCE_ACQUIRE_COUNTERAGENTS,
            [],
            [
                'myOrgId' => $myOrgId,
                'counteragentOrgId' => $counteragentOrgId,
                'myDepartmentId' => $myDepartmentId,
                'comment' => $comment,
            ],
            self::METHOD_POST
        );
    }

    /**
     * @param InvitationDocument|null $invitationDocument |null $invationDocument
     *
     * @throws DiadocApiException
     */
    public function acquireCounteragentWithDocument(string $myOrgId, string $counteragentOrgId, string $myDepartmentId, ?InvitationDocument $invitationDocument = null, string $messageToContragent = ''): AsyncMethodResult
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
                'myDepartmentId' => $myDepartmentId,
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
     * @throws DiadocApiException
     */
    public function acquireCounteragentByInnWithDocument(string $myOrgId, string $counteragentInn, ?string $myDepartmentId = null, ?InvitationDocument $invitationDocument = null, string $messageToContragent = ''): AsyncMethodResult
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
                'myDepartmentId' => $myDepartmentId,
            ],
            self::METHOD_POST
        );

        $asyncMethodResult = new AsyncMethodResult();
        $asyncMethodResult->mergeFromString($response);

        return $asyncMethodResult;
    }

    /**
     * @throws DiadocApiException
     */
    public function acquireCounteragentResult(string $taskId): AcquireCounteragentResult
    {
        $response = $this->doRequest(
            self::RESOURCE_ACQUIRE_COUNTERAGENT_RESULT,
            [],
            [
                'taskId' => $taskId,
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
                'comment' => $comment,
            ],
            self::METHOD_POST
        );
    }

    /**
     * @throws DiadocApiException
     */
    public function getCountragent(string $myOrgId, string $counteragentOrgId): Counteragent
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_COUNTERAGENT,
            [],
            [
                'myOrgId' => $myOrgId,
                'counteragentOrgId' => $counteragentOrgId,
            ]
        );
        $counteragent = new Counteragent();
        $counteragent->mergeFromString($response);

        return $counteragent;
    }

    /**
     * @throws DiadocApiException
     */
    public function getCountragentV2(string $myOrgId, string $counteragentOrgId): Counteragent
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_COUNTERAGENT_V2,
            [],
            [
                'myOrgId' => $myOrgId,
                'counteragentOrgId' => $counteragentOrgId,
            ]
        );

        $counteragent = new Counteragent();
        $counteragent->mergeFromString($response);

        return $counteragent;
    }

    /**
     * @throws DiadocApiException
     * @throws DiadocApiUnauthorizedException
     */
    public function getCountragents(string $myOrgId, ?string $counteragentStatus = null, ?int $afterIndexKey = null): CounteragentList
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_COUNTERAGENTS,
            [],
            [
                'myOrgId' => $myOrgId,
                'counteragentStatus' => $counteragentStatus,
                'afterIndexKey' => $afterIndexKey,
            ]
        );
        $counteragentList = new CounteragentList();
        $counteragentList->mergeFromString($response);

        return $counteragentList;
    }

    public function getCountragentsV2(string $myOrgId, ?string $counteragentStatus = null, ?string $afterIndexKey = null, ?string $query = null): CounteragentList
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_COUNTERAGENTS_V2,
            [],
            [
                'myOrgId' => $myOrgId,
                'counteragentStatus' => $counteragentStatus,
                'afterIndexKey' => $afterIndexKey,
                'query' => $query,
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
                'counteragentOrgId' => $counteragentOrgId,
            ]
        );

        $counteragentCertificateList = new CounteragentCertificateList();
        $counteragentCertificateList->mergeFromString($response);

        return $counteragentCertificateList;
    }

    /**
     * @throws DiadocApiException
     */
    public function getEntityContent(string $boxId, string $messageId, string $entityId): string
    {
        return $this->doRequest(
            self::RESOURCE_GET_ENTITY_CONTENT,
            [],
            [
                'boxId' => $boxId,
                'messageId' => $messageId,
                'entityId' => $entityId,
            ]
        );
    }

    /**
     * @throws DiadocApiException
     */
    public function getMessage(string $boxId, string $messageId, ?string $entityId = null, ?string $originalSignature = null): Message
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_MESSAGE,
            [],
            [
                'boxId' => $boxId,
                'messageId' => $messageId,
                'entityId' => $entityId,
                'originalSignature' => $originalSignature,
            ]
        );
        $message = new Message();
        $message->mergeFromString($response);

        return $message;
    }

    /**
     * @throws DiadocApiException
     */
    public function getMessageV6(
        string $boxId,
        string $messageId,
        ?string $entityId = null,
        ?string $originalSignature = null,
        string $injectEntityContent = 'true'
    ): Message {
        $response = $this->doRequest(
            self::GET_MESSAGE_V6,
            [],
            [
                'boxId' => $boxId,
                'messageId' => $messageId,
                'entityId' => $entityId,
                'originalSignature' => $originalSignature,
                'injectEntityContent' => $injectEntityContent,
            ]
        );
        $message = new Message();
        $message->mergeFromString($response);

        return $message;
    }

    /**
     * @throws DiadocApiException
     * @throws DiadocApiUnauthorizedException
     */
    public function postMessage(MessageToPost $messageToPost, ?string $operationId = null): Message
    {
        $response = $this->doRequest(
            self::RESOURCE_POST_MESSAGE,
            $messageToPost->serializeToString(),
            [
                'operationId' => $operationId,
            ],
            self::METHOD_POST
        );

        $message = new Message();
        $message->mergeFromString($response);

        return $message;
    }

    /**
     * @throws DiadocApiException
     */
    public function postMessagePatch(MessagePatchToPost $messagePatchToPost, ?string $operationId = null): MessagePatch
    {
        $response = $this->doRequest(
            self::RESOURCE_POST_MESSAGE_PATCH,
            $messagePatchToPost->serializeToString(),
            [
                'operationId' => $operationId,
            ],
            self::METHOD_POST
        );

        $messagePatch = new MessagePatch();
        $messagePatch->mergeFromString($response);

        return $messagePatch;
    }

    /**
     * @throws DiadocApiException
     */
    public function delete(string $boxId, string $messageId, ?string $documentId = null): bool
    {
        $this->doRequest(
            self::RESOURCE_DELETE,
            [],
            [
                'boxId' => $boxId,
                'messageId' => $messageId,
                'documentId' => $documentId,
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
                'boxId' => $boxId,
            ],
            self::METHOD_POST
        );
    }

    /**
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
                'entityId' => $entityId,
                'injectEntityContent' => $injectEntityContent,
            ]
        );
        $document = new Document();
        $document->mergeFromString($response);

        return $document;
    }

    public function getDocuments(string $boxId, ?DocumentsFilter $documentsFilter = null, ?int $sortDirection = null, ?int $afterIndexKey = null): DocumentList
    {
        $sortDirection ??= SortDirection::Ascending;

        $params = [
            'boxId' => $boxId,
            'sortDirection' => $sortDirection,
            'afterIndexKey' => $afterIndexKey,
        ];
        $documentsFilter ??= DocumentsFilter::create();

        $params = \array_replace($params, $documentsFilter->toFilter());

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
     * @throws DiadocApiException
     */
    public function getDocflows(string $boxId, GetDocflowBatchRequest $getDocflowBatchRequest): GetDocflowBatchResponse
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_DOCFLOWS,
            $getDocflowBatchRequest->serializeToString(),
            [
                'boxId' => $boxId,
            ],
            self::METHOD_POST
        );

        $getDocflowBatchResponse = new GetDocflowBatchResponse();
        $getDocflowBatchResponse->mergeFromString($response);

        return $getDocflowBatchResponse;
    }

    /**
     * @throws DiadocApiException
     */
    public function getDocflowsV5(string $boxId, GetDocflowBatchRequest $getDocflowBatchRequest): GetDocflowBatchResponseV5
    {
        $response = $this->doRequest(
            self::GET_DOCFLOWS_V5,
            $getDocflowBatchRequest->serializeToString(),
            [
                'boxId' => $boxId,
            ],
            self::METHOD_POST
        );

        $getDocflowBatchResponseV5 = new GetDocflowBatchResponseV5();
        $getDocflowBatchResponseV5->mergeFromString($response);

        return $getDocflowBatchResponseV5;
    }

    /**
     * @throws DiadocApiException
     */
    public function getDocflowsByPacketId(string $boxId, string $packetId, bool $injectEntityContent = false, ?int $afterIndexKey = null, int $count = 100): GetDocflowsByPacketIdResponse
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
                'boxId' => $boxId,
            ],
            self::METHOD_POST
        );

        $getDocflowsByPacketIdResponse = new GetDocflowsByPacketIdResponse();
        $getDocflowsByPacketIdResponse->mergeFromString($response);

        return $getDocflowsByPacketIdResponse;
    }

    /**
     * @throws DiadocApiException
     */
    public function searchDocflows(string $boxId, string $queryString, ?int $searchScope = null, ?int $firstIndex = null, int $count = 100): SearchDocflowsResponse
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
                'boxId' => $boxId,
            ],
            self::METHOD_POST
        );

        $searchDocflowsResponse = new SearchDocflowsResponse();
        $searchDocflowsResponse->mergeFromString($response);

        return $searchDocflowsResponse;
    }

    /**
     * @throws DiadocApiException
     */
    public function getDocflowEvents(
        string $boxId,
        ?DateTime $from = null,
        ?DateTime $to = null,
        ?int $sortDirection = null,
        bool $populateDocuments = false,
        bool $populatePreviousDocumentStates = false,
        bool $injectEntityContent = false,
        ?int $afterIndexKey = null
    ): GetDocflowEventsResponse {
        $timeBasedFilter = new TimeBasedFilter();
        $fromTimestamp = null;
        $toTimestamp = null;

        if ($from instanceof \DateTime) {
            $fromTimestamp = new Timestamp();
            $fromTimestamp->setTicks(DateHelper::convertDateTimeToTicks($from));
        }

        if ($to instanceof \DateTime) {
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
                'boxId' => $boxId,
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
                'eventId' => $eventId,
            ]
        );

        $boxEvent = new BoxEvent();
        $boxEvent->mergeFromString($response);

        return $boxEvent;
    }

    public function getNewEvents(string $boxId, ?string $afterEventId = null): BoxEventList
    {
        $response = $this->doRequest(
            self::RESOURCE_GET_NEW_EVENTS,
            [],
            [
                'boxId' => $boxId,
                'afterEventId' => $afterEventId,
            ]
        );
        $boxEventList = new BoxEventList();
        $boxEventList->mergeFromString($response);

        return $boxEventList;
    }

    public function getNewEventsV8(
        string $boxId,
        ?string $afterIndexKey = null,
        ?string $afterEventId = null,
        ?string $messageType = null,
        ?string $typeNamedId = null,
        ?string $documentDirection = null,
        ?int $timestampFromTicks = null,
        ?int $timestampToTicks = null,
        ?string $counteragentBoxId = null,
        ?string $orderBy = null,
        ?int $limit = null
    ): BoxEventList {
        $response = $this->doRequest(
            self::RESOURCE_GET_NEW_EVENTS_V8,
            [],
            [
                'boxId' => $boxId,
                'afterIndexKey' => $afterIndexKey,
                'afterEventId' => $afterEventId,
                'messageType' => $messageType,
                'typeNamedId' => $typeNamedId,
                'documentDirection' => $documentDirection,
                'timestampFromTicks' => $timestampFromTicks,
                'timestampToTicks' => $timestampToTicks,
                'counteragentBoxId' => $counteragentBoxId,
                'orderBy' => $orderBy,
                'limit' => $limit,
            ]
        );
        $boxEventList = new BoxEventList();
        $boxEventList->mergeFromString($response);

        return $boxEventList;
    }

    public function getToken(): ?string
    {
        return $this->authMode->getToken();
    }

    public function setToken(?string $token): void
    {
        $this->authMode->setToken($token);
    }

    /**
     * Установить логгер HTTP-запросов (например, {@see \MagDv\Diadoc\Logger\StdoutHttpLogger}).
     *
     * @param HttpLoggerInterface|null $httpLogger логгер или null, чтобы отключить логирование
     */
    public function setLogger(?HttpLoggerInterface $httpLogger): void
    {
        $this->httpLogger = $httpLogger;
    }

    /**
     * Текущий режим авторизации (self::AUTH_MODE_OIDC | self::AUTH_MODE_AUTHENTICATE_V3).
     */
    public function getAuthMode(): string
    {
        return $this->authMode->getMode();
    }

    /**
     * Установить ddauth-токен для режима authenticate_v3.
     *
     * @throws DiadocApiException если текущий режим не authenticate_v3
     */
    public function setLegacyToken(?string $token): void
    {
        if (! $this->authMode instanceof AuthenticateV3AuthMode) {
            throw new DiadocApiException(
                'setLegacyToken() доступен только при auth mode ' . self::AUTH_MODE_AUTHENTICATE_V3,
                0
            );
        }

        $this->authMode->setToken($token);
    }

    /**
     * URL редиректа на страницу авторизации Контур (Authorization Code Flow).
     *
     * @see https://developer.kontur.ru/docs/diadoc-api/authentication.html
     *
     * @throws DiadocApiException если текущий режим не OIDC
     */
    public function buildAuthorizationUrl(string $redirectUri, string $state, ?string $nonce = null): string
    {
        return $this->oidc()
            ->buildAuthorizationUrl($redirectUri, $state, $nonce);
    }

    /**
     * Обмен authorization code на access/refresh токены.
     *
     * @throws DiadocApiException
     */
    public function exchangeAuthorizationCode(string $code, string $redirectUri): void
    {
        $this->oidc()
            ->exchangeAuthorizationCode($code, $redirectUri);
    }

    /**
     * Обновление access_token по refresh_token.
     *
     * @throws DiadocApiException
     */
    public function refreshAccessToken(): void
    {
        $this->oidc()
            ->refreshAccessToken();
    }

    /**
     * Установить токены вручную (например из кеша после перезапуска).
     *
     * @param int|null $expiresAtUnix время истечения access_token (unix), null — неизвестно (без проактивного refresh)
     */
    public function setOAuthSession(string $accessToken, ?string $refreshToken = null, ?int $expiresAtUnix = null): void
    {
        $this->oidc()
            ->setOAuthSession($accessToken, $refreshToken, $expiresAtUnix);
    }

    /**
     * @return array{access_token: string, refresh_token: ?string, expires_at: ?int}
     */
    public function getOAuthSessionState(): array
    {
        return $this->oidc()
            ->getOAuthSessionState();
    }

    /**
     * Колбэк вызывается после обмена кода, refresh и при {@see setOAuthSession}, чтобы сохранить сессию (файл, БД).
     *
     * @param callable|null $callback function (array $session): void
     */
    public function setOAuthSessionPersistenceCallback(?callable $callback): void
    {
        $this->oidc()
            ->setOAuthSessionPersistenceCallback($callback);
    }

    /**
     * OIDC scope для текущего окружения (Diadoc.PublicAPI / Diadoc.PublicAPI.Staging).
     */
    public function getOidcScope(): string
    {
        return $this->oidc()
            ->getOidcScope();
    }

    /**
     * @throws DiadocApiException если текущий режим не OIDC
     */
    private function oidc(): OidcAuthFlowAuthMode
    {
        if (! $this->authMode instanceof OidcAuthFlowAuthMode) {
            throw new DiadocApiException(
                'Метод доступен только при auth mode ' . self::AUTH_MODE_OIDC . '. Используйте DiadocApi::create() с OidcAuthFlowAuthMode.',
                0
            );
        }

        return $this->authMode;
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
        if (! \file_exists($fileName)) {
            throw new \Exception('File not found');
        }

        $content = \file_get_contents($fileName);

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
            [
                'content' => $content,
            ],
            [
                'nameOnShelf' => $nameOnShelf,
                'partIndex' => $partIndex,
                'isLastPart' => $isLastPart,
            ],
            self::METHOD_POST,
            self::CONTENT_FORM_URL_ENCODED
        );
    }
}
