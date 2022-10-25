<?php

namespace MagDv\Diadoc;

use MagDv\Diadoc\Exception\DiadocApiException;
use Google\Protobuf\Internal\Message;
use Diadoc\Proto\AsyncMethodResult;
use Diadoc\Proto\Counteragent;
use Diadoc\Proto\CounteragentCertificateList;
use Diadoc\Proto\CounteragentList;
use Diadoc\Proto\CounteragentStatus;
use Diadoc\Proto\Department;
use Diadoc\Proto\GetOrganizationsByInnListResponse;
use Diadoc\Proto\InvitationDocument;
use Diadoc\Proto\Organization;
use Diadoc\Proto\OrganizationUserPermissions;
use Diadoc\Proto\OrganizationUsersList;

class OrganizationApi
{
    /** @var  DiadocApi */
    private $diadocApi;
    /** @var  string */
    private $orgId;

    /**
     * @param DiadocApi $diadocApi
     * @param string $orgId
     */
    public function __construct(DiadocApi $diadocApi, string $orgId)
    {
        $this->diadocApi = $diadocApi;
        $this->orgId = $orgId;
    }


    /**
     * @return DiadocApi
     */
    public function getDiadocApi(): DiadocApi
    {
        return $this->diadocApi;
    }

    /**
     * @return Organization
     * @throws DiadocApiException
     */
    public function getOrganization(): Organization
    {
        return $this->diadocApi->getOrganizationById($this->orgId);
    }

    /**
     * @param string $departmentId
     * @return Department
     * @throws DiadocApiException
     */
    public function getDepartment(string $departmentId): Department
    {
        return $this->diadocApi->getDepartment($this->orgId, $departmentId);
    }

    /**
     * @return OrganizationUserPermissions
     * @throws DiadocApiException
     */
    public function getMyPermissions(): OrganizationUserPermissions
    {
        return $this->diadocApi->getMyPermissions($this->orgId);
    }

    /**
     * @param array $innList
     * @return GetOrganizationsByInnListResponse
     * @throws DiadocApiException
     */
    public function getOrganizationsByInnList(array $innList = []): GetOrganizationsByInnListResponse
    {
        return $this->diadocApi->getOrganizationsByInnList($this->orgId, $innList);
    }

    /**
     * @return OrganizationUsersList
     * @throws DiadocApiException
     */
    public function getOrganizationUsers(): OrganizationUsersList
    {
        return $this->diadocApi->getOrganizationUsers($this->orgId);
    }

    /**
     * @param string $counteragentOrgId
     * @param string $myDepartmentId
     * @param null $comment
     *
     * @return mixed
     * @throws DiadocApiException
     */
    public function acquireCounteragent(string $counteragentOrgId, string $myDepartmentId, $comment = null): string
    {
        return $this->diadocApi->acquireCounteragent($this->orgId, $counteragentOrgId, $myDepartmentId, $comment);
    }

    /**
     * @param string $counteragentOrgId
     * @param string $myDepartmentId
     * @param InvitationDocument|null $invitationDocument
     * @param string|null $messageToContragent
     *
     * @return AsyncMethodResult
     * @throws DiadocApiException
     */
    public function acquireCounteragentWithDocument(string $counteragentOrgId, string  $myDepartmentId, InvitationDocument $invitationDocument = null, ?string $messageToContragent = null): AsyncMethodResult
    {
        return $this->diadocApi->acquireCounteragentWithDocument($this->orgId, $counteragentOrgId, $myDepartmentId, $invitationDocument, $messageToContragent);
    }

    /**
     * @param string $counteragentInn
     * @param string|null $myDepartmentId
     * @param InvitationDocument|null $invitationDocument
     * @param string|null $messageToContragent
     *
     * @return AsyncMethodResult|Message
     * @throws DiadocApiException
     */
    public function acquireCounteragentByInnWithDocument(string $counteragentInn, string $myDepartmentId = null, InvitationDocument $invitationDocument = null, ?string $messageToContragent = null): AsyncMethodResult
    {
        return $this->diadocApi->acquireCounteragentByInnWithDocument($this->orgId, $counteragentInn, $myDepartmentId, $invitationDocument, $messageToContragent);
    }

    /**
     * @param string $counteragentOrgId
     * @param string|null $comment
     *
     * @return string
     * @throws DiadocApiException
     */
    public function breakWithCounteragent(string $counteragentOrgId, ?string $comment = null): string
    {
        return $this->diadocApi->breakWithCounteragent($this->orgId, $counteragentOrgId, $comment);
    }

    /**
     * @param string $counteragentOrgId
     *
     * @return Counteragent
     * @throws DiadocApiException
     */
    public function getCountragent(string $counteragentOrgId): Counteragent
    {
        return $this->diadocApi->getCountragent($this->orgId, $counteragentOrgId);
    }

    /**
     * @param string $counteragentOrgId
     *
     * @return Counteragent
     * @throws DiadocApiException
     */
    public function getCountragentV2(string $counteragentOrgId): Counteragent
    {
        return $this->diadocApi->getCountragentV2($this->orgId, $counteragentOrgId);
    }

    /**
     * @param CounteragentStatus|null $counteragentStatus
     * @param int|null $afterIndexKey
     *
     * @return CounteragentList
     * @throws DiadocApiException
     */
    public function getCountragents(CounteragentStatus $counteragentStatus = null, ?int $afterIndexKey = null): CounteragentList
    {
        return $this->diadocApi->getCountragents($this->orgId, $counteragentStatus, $afterIndexKey);
    }

    /**
     * @param CounteragentStatus|null $counteragentStatus
     * @param int|null $afterIndexKey
     *
     * @return CounteragentList
     * @throws DiadocApiException
     */
    public function getCountragentsV2(CounteragentStatus $counteragentStatus = null, ?int $afterIndexKey = null): CounteragentList
    {
        return $this->diadocApi->getCountragentsV2($this->orgId, $counteragentStatus, $afterIndexKey);
    }

    /**
     * @param string $counteragentOrgId
     * @return CounteragentCertificateList
     */
    public function getCounteragentCertificates(string $counteragentOrgId): CounteragentCertificateList
    {
        return $this->diadocApi->getCounteragentCertificates($this->orgId, $counteragentOrgId);
    }
}
