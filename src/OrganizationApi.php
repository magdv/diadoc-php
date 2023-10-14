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
    public function __construct(private DiadocApi $diadocApi, private string $orgId)
    {
    }


    public function getDiadocApi(): DiadocApi
    {
        return $this->diadocApi;
    }

    /**
     * @throws DiadocApiException
     */
    public function getOrganization(): Organization
    {
        return $this->diadocApi->getOrganizationById($this->orgId);
    }

    /**
     * @throws DiadocApiException
     */
    public function getDepartment(string $departmentId): Department
    {
        return $this->diadocApi->getDepartment($this->orgId, $departmentId);
    }

    /**
     * @throws DiadocApiException
     */
    public function getMyPermissions(): OrganizationUserPermissions
    {
        return $this->diadocApi->getMyPermissions($this->orgId);
    }

    /**
     * @throws DiadocApiException
     */
    public function getOrganizationsByInnList(array $innList = []): GetOrganizationsByInnListResponse
    {
        return $this->diadocApi->getOrganizationsByInnList($this->orgId, $innList);
    }

    /**
     * @throws DiadocApiException
     */
    public function getOrganizationUsers(): OrganizationUsersList
    {
        return $this->diadocApi->getOrganizationUsers($this->orgId);
    }

    /**
     * @param null $comment
     * @return mixed
     * @throws DiadocApiException
     */
    public function acquireCounteragent(string $counteragentOrgId, string $myDepartmentId, $comment = null): string
    {
        return $this->diadocApi->acquireCounteragent($this->orgId, $counteragentOrgId, $myDepartmentId, $comment);
    }

    /**
     * @param InvitationDocument|null $invitationDocument
     *
     * @throws DiadocApiException
     */
    public function acquireCounteragentWithDocument(string $counteragentOrgId, string $myDepartmentId, InvitationDocument $invitationDocument = null, ?string $messageToContragent = null): AsyncMethodResult
    {
        return $this->diadocApi->acquireCounteragentWithDocument($this->orgId, $counteragentOrgId, $myDepartmentId, $invitationDocument, $messageToContragent);
    }

    /**
     * @param string|null $myDepartmentId
     * @param InvitationDocument|null $invitationDocument
     *
     * @return AsyncMethodResult|Message
     * @throws DiadocApiException
     */
    public function acquireCounteragentByInnWithDocument(string $counteragentInn, string $myDepartmentId = null, InvitationDocument $invitationDocument = null, ?string $messageToContragent = null): AsyncMethodResult
    {
        return $this->diadocApi->acquireCounteragentByInnWithDocument($this->orgId, $counteragentInn, $myDepartmentId, $invitationDocument, $messageToContragent);
    }

    /**
     *
     * @throws DiadocApiException
     */
    public function breakWithCounteragent(string $counteragentOrgId, ?string $comment = null): string
    {
        return $this->diadocApi->breakWithCounteragent($this->orgId, $counteragentOrgId, $comment);
    }

    /**
     * @throws DiadocApiException
     */
    public function getCountragent(string $counteragentOrgId): Counteragent
    {
        return $this->diadocApi->getCountragent($this->orgId, $counteragentOrgId);
    }

    /**
     * @throws DiadocApiException
     */
    public function getCountragentV2(string $counteragentOrgId): Counteragent
    {
        return $this->diadocApi->getCountragentV2($this->orgId, $counteragentOrgId);
    }

    /**
     * @param CounteragentStatus|null $counteragentStatus
     *
     * @throws DiadocApiException
     */
    public function getCountragents(CounteragentStatus $counteragentStatus = null, ?int $afterIndexKey = null): CounteragentList
    {
        return $this->diadocApi->getCountragents($this->orgId, $counteragentStatus, $afterIndexKey);
    }

    /**
     * @param CounteragentStatus|null $counteragentStatus
     *
     * @throws DiadocApiException
     */
    public function getCountragentsV2(CounteragentStatus $counteragentStatus = null, ?int $afterIndexKey = null): CounteragentList
    {
        return $this->diadocApi->getCountragentsV2($this->orgId, $counteragentStatus, $afterIndexKey);
    }

    public function getCounteragentCertificates(string $counteragentOrgId): CounteragentCertificateList
    {
        return $this->diadocApi->getCounteragentCertificates($this->orgId, $counteragentOrgId);
    }
}
