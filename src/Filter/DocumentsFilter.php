<?php

namespace MagDv\Diadoc\Filter;

use DateTime;
use Diadoc\Proto\DocumentType;
use MagDv\Diadoc\Helper\DateHelper;

class DocumentsFilter
{
    /**
     * Соответствует набору из четырех типов документов СФ/ИСФ/КСФ/ИКСФ
     * (Invoice, InvoiceRevision, InvoiceCorrection, InvoiceCorrectionRevision)
     *
     * @see DocumentType
     * @var string
     */
    public const DOCUMENT_TYPE_ANY = 'Any';

    /**
     * соответствует любому типу двусторонних документов
     * (Nonformalized, Torg12, AcceptanceCertificate, XmlTorg12,
     * XmlAcceptanceCertificate, TrustConnectionRequest, PriceList,
     * PriceListAgreement, CertificateRegistry, ReconciliationAct, Contract,
     * Torg13)
     *
     * @see DocumentType
     * @var string
     */
    public const DOCUMENT_TYPE_ANY_INVOICE = 'AnyInvoiceDocumentType';

    /**
     * соответствует любому типу односторонних документов
     * (ProformaInvoice, ServiceDetails)
     *
     * @see DocumentType
     * @var string
     */
    public const DOCUMENT_TYPE_ANY_BILATERAL = 'AnyBilateralDocumentType';

    /**
     * соответствует любому типу документа.
     *
     * @see DocumentType
     * @var string
     */
    public const DOCUMENT_TYPE_ANY_UNILATERAL = 'AnyUnilateralDocumentType';

    /** входящий документ
     * @var string */
    public const DOCUMENT_CLASS_INBOUND = 'Inbound';

    /** исходящий документ
     * @var string */
    public const DOCUMENT_CLASS_OUTBOUND = 'Outbound';

    /** внутренний документ
     * @var string */
    public const DOCUMENT_CLASS_INTERNAL = 'Internal';

    /** любой документ указанного класса
     * @var string */
    public const DOCUMENT_STATUS_ANY = '';

    /** документ не прочитан
     * @var string */
    public const DOCUMENT_STATUS_NOT_READ = 'NotRead';

    /** документ без запроса ответной подписи
     * @var string */
    public const DOCUMENT_STATUS_NO_RECIPIENT_SIGNATURE_REQUEST = 'NoRecipientSignatureRequest';

    /** документ в ожидании ответной подписи
     * @var string */
    public const DOCUMENT_STATUS_WAIT_FOR_RECIPIENT_SIGNATURE = 'WaitingForRecipientSignature';

    /** документ с ответной подписью
     * @var string */
    public const DOCUMENT_STATUS_WITH_RECIPIENT_SIGNATURE = 'WithRecipientSignature';

    /** документ с подписью отправителя
     * @var string */
    public const DOCUMENT_STATUS_WITH_SENDER_SIGNATURE = 'WithSenderSignature';

    /** документ с отказом от формирования ответной подписи
     * @var string */
    public const DOCUMENT_STATUS_RECIPIENT_SIGNATURE_REQUEST_REJECT = 'RecipientSignatureRequestRejected';

    /** документ, требующий подписания и отправки
     * @var string */
    public const DOCUMENT_STATUS_WAIT_FOR_SENDER_SIGNATURE = 'WaitingForSenderSignature';

    /** документ с невалидной подписью отправителя, требующий повторного подписания и отправки
     * @var string */
    public const DOCUMENT_STATUS_INVALID_SENDER_SIGNATURE = 'InvalidSenderSignature';

    /** документ с невалидной подписью получателя, требующий повторного подписания и отправки
     * @var string */
    public const DOCUMENT_STATUS_INVALID_RECIPIENT_SIGNATURE = 'InvalidRecipientSignature';

    /** согласованный документ
     * @var string */
    public const DOCUMENT_STATUS_APPROVED = 'Approved';

    /** документ с отказом согласования
     * @var string */
    public const DOCUMENT_STATUS_DISAPPROVED = 'Disapproved';

    /** документ, находящийся на согласовании или подписи
     * @var string */
    public const DOCUMENT_STATUS_WAITING_FOR_RESOLUTION = 'WaitingForResolution';

    /** документ с отказом в запросе подписи сотруднику
     * @var string */
    public const DOCUMENT_STATUS_SIGNATURE_REQUEST_REJECTED = 'SignatureRequestRejected';

    /** документ с завершенным документооборотом
     * @var string */
    public const DOCUMENT_STATUS_FINISHED = 'Finished';

    /** требуется подписать извещение о получении
     * @var string */
    public const DOCUMENT_STATUS_HAVE_TO_CREATE_RECEIPT = 'HaveToCreateReceipt';

    /** документ с незавершенным документооборотом
     * @var string */
    public const DOCUMENT_STATUS_NOT_FINISHED = 'NotFinished';

    /** имеет смысл только для счетов-фактур; документ, по которому было запрошено уточнение
     * @var string */
    public const DOCUMENT_STATUS_INVOICE_AMENDMENT_REQUESTED = 'InvoiceAmendmentRequested';

    /** документ, по которому было запрошено аннулирование
     * @var string */
    public const DOCUMENT_STATUS_REVOCATION_IS_REQUESTED_BY_ME = 'RevocationIsRequestedByMe';

    /** документ, по которому контрагент запросил аннулирование
     * @var string */
    public const DOCUMENT_STATUS_REQUESTS_MY_REVOCATION = 'RequestsMyRevocation';

    /** аннулированный документ
     * @var string */
    public const DOCUMENT_STATUS_REVOCATION_ACCEPTED = 'RevocationAccepted';

    /** документ, запрос на аннулирование которого был отклонен
     * @var string */
    public const DOCUMENT_STATUS_REVOCATION_REJECTED = 'RevocationRejected';

    /** документ, запрос на аннулирование которого был согласован
     * @var string */
    public const DOCUMENT_STATUS_REVOCATION_APPROVED = 'RevocationApproved';

    /** документ с отказом согласования запроса на аннулирование
     * @var string */
    public const DOCUMENT_STATUS_REVOCATION_DISAPPROVED = 'RevocationDisapproved';

    /** документ, находящийся на согласовании запроса аннулирования
     * @var string */
    public const DOCUMENT_STATUS_WAITING_FOR_REVOCATION_APPROVEMENT = 'WaitingForRevocationApprovement';

    /** неаннулированный документ
     * @var string */
    public const DOCUMENT_STATUS_NOT_REVOKED = 'NotRevoked';

    /** документ в ожидании подписи промежуточного получателя
     * @var string */
    public const DOCUMENT_STATUS_WAITING_FOR_PROXY_SIGNATURE = 'WaitingForProxySignature';

    /** документ с подписью промежуточного получателя
     * @var string */
    public const DOCUMENT_STATUS_WITH_PROXY_SIGNATURE = 'WithProxySignature';

    /** документ с невалидной подписью промежуточного получателя, требующий повторного подписания и отправки
     * @var string */
    public const DOCUMENT_STATUS_INVALID_PROXY_SIGNATURE = 'InvalidProxySignature';

    /** документ с отказом от формирования подписи промежуточным получателем
     * @var string */
    public const DOCUMENT_STATUS_PROXY_SIGNATURE_REJECTED = 'ProxySignatureRejected';

    /** документ в ожидании получения извещения о получении счета-фактуры
     * @var string */
    public const DOCUMENT_STATUS_WAITING_FOR_INVOICE_RECEIPT = 'WaitingForInvoiceReceipt';

    /** документ в ожидании получения извещения о получении
     * @var string */
    public const DOCUMENT_STATUS_WAITING_FOR_RECEIPT = 'WaitingForReceipt';

    /** документ, по которому контрагент запросил подпись
     * @var string */
    public const DOCUMENT_STATUS_REQUESTS_MY_SIGNATURE = 'RequestsMySignature';

    /** документ, с ошибкой доставки в роуминге
     * @var string */
    public const DOCUMENT_STATUS_ROAMING_NOTIFICATION_ERROR = 'RoamingNotificationError';


    private string $filterDocumentType = self::DOCUMENT_TYPE_ANY;

    private string $filterDocumentClass = self::DOCUMENT_CLASS_INTERNAL;

    private string $filterDocumentStatus = self::DOCUMENT_STATUS_ANY;

    private ?string $counteragentBoxId = null;

    private ?string $toDepartmentId = null;

    private ?\DateTime $fromDate = null;

    private ?\DateTime $toDate = null;

    private ?\DateTime $fromDocumentDate = null;

    private ?\DateTime $toDocumentDate = null;

    private ?string $departmentId = null;

    private ?bool $excludeSubdepartments = null;

    public static function create()
    {
        return new self();
    }

    public function getFilterDocumentType(): string
    {
        return $this->filterDocumentType;
    }

    /**
     * @return $this
     */
    public function setFilterDocumentType(string $filterDocumentType): self
    {
        $this->filterDocumentType = $filterDocumentType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFilterDocumentClass(): string
    {
        return $this->filterDocumentClass;
    }

    /**
     * @return $this
     */
    public function setFilterDocumentClass(string $filterDocumentClass): self
    {
        $this->filterDocumentClass = $filterDocumentClass;

        return $this;
    }

    public function getFilterDocumentStatus(): string
    {
        return $this->filterDocumentStatus;
    }

    /**
     * @return $this
     */
    public function setFilterDocumentStatus(string $filterDocumentStatus): self
    {
        $this->filterDocumentStatus = $filterDocumentStatus;

        return $this;
    }

    public function getCounteragentBoxId(): ?string
    {
        return $this->counteragentBoxId;
    }

    /**
     * @return $this
     */
    public function setCounteragentBoxId(string $counteragentBoxId): self
    {
        $this->counteragentBoxId = $counteragentBoxId;
        return $this;
    }

    public function getToDepartmentId(): ?string
    {
        return $this->toDepartmentId;
    }

    /**
     * @return $this
     */
    public function setToDepartmentId(string $toDepartmentId): self
    {
        $this->toDepartmentId = $toDepartmentId;
        return $this;
    }

    public function getFromDate(): DateTime
    {
        return $this->fromDate;
    }

    /**
     * @param \DateTime|null $fromDate
     * @return $this
     */
    public function setFromDate(DateTime $fromDate = null): self
    {
        $this->fromDate = $fromDate;
        return $this;
    }

    public function getToDate(): DateTime
    {
        return $this->toDate;
    }

    /**
     * @param \DateTime|null $toDate
     * @return $this
     */
    public function setToDate(DateTime $toDate = null): self
    {
        $this->toDate = $toDate;
        return $this;
    }

    public function getFromDocumentDate(): ?DateTime
    {
        return $this->fromDocumentDate;
    }

    /**
     * @return $this
     */
    public function setFromDocumentDate(DateTime $fromDocumentDate = null): self
    {
        $this->fromDocumentDate = $fromDocumentDate;
        return $this;
    }

    public function getToDocumentDate(): ?DateTime
    {
        return $this->toDocumentDate;
    }

    /**
     * @return $this
     */
    public function setToDocumentDate(DateTime $toDocumentDate = null): self
    {
        $this->toDocumentDate = $toDocumentDate;
        return $this;
    }

    public function getDepartmentId(): ?string
    {
        return $this->departmentId;
    }

    /**
     * @return $this
     */
    public function setDepartmentId(string $departmentId): self
    {
        $this->departmentId = $departmentId;
        return $this;
    }

    public function isExcludeSubdepartments(): ?bool
    {
        return $this->excludeSubdepartments;
    }

    /**
     * @return $this
     */
    public function setExcludeSubdepartments(bool $excludeSubdepartments): self
    {
        $this->excludeSubdepartments = $excludeSubdepartments;
        return $this;
    }

    private function buildFilterCategory(): string
    {
        return sprintf('%s.%s%s', $this->filterDocumentType, $this->filterDocumentClass, $this->filterDocumentStatus);
    }

    public function toFilter(): array
    {
        return [
            'filterCategory'    => $this->buildFilterCategory(),
            'counteragentBoxId' => $this->counteragentBoxId,
            'toDepartmentId'    => $this->toDepartmentId,
            'timestampFromTicks' => $this->fromDate !== null ? DateHelper::convertDateTimeToTicks($this->fromDate) : null,
            'timestampToTicks'  => $this->toDate !== null ? DateHelper::convertDateTimeToTicks($this->toDate) : null,
            'fromDocumentDate'  => $this->fromDocumentDate !== null ? $this->fromDocumentDate->format('d.m.Y') : null,
            'toDocumentDate'    => $this->toDocumentDate !== null ? $this->toDocumentDate->format('d.m.Y') : null,
            'departmentId'      => $this->departmentId,
            'excludeSubdepartments' => $this->excludeSubdepartments
        ];
    }
}
