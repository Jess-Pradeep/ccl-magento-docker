<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CompanyUserInterface
 */
interface CompanyUserInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    public const CUSTOMER_ID = 'customer_id';
    public const COMPANY_ID = 'company_id';
    public const IS_ROOT = 'is_root';
    public const STATUS = 'status';
    public const COMPANY_GROUP_ID = 'company_group_id';
    public const COMPANY_ROLE_ID = 'company_role_id';
    public const COMPANY_UNIT_ID = 'company_unit_id';
    public const JOB_TITLE = 'job_title';
    public const TELEPHONE = 'telephone';
    public const ADDITIONAL_INFO = 'additional_info';
    /**#@-*/

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get company id
     *
     * @return int
     */
    public function getCompanyId();

    /**
     * Set company id
     *
     * @param int $companyId
     * @return $this
     */
    public function setCompanyId($companyId);

    /**
     * Get is root flag
     *
     * @return boolean
     */
    public function getIsRoot();

    /**
     * Set is root flag
     *
     * @param boolean $isRoot
     * @return $this
     */
    public function setIsRoot($isRoot);

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get company group id
     *
     * @return int
     */
    public function getCompanyGroupId();

    /**
     * Set company group id
     *
     * @param int $groupId
     * @return $this
     */
    public function setCompanyGroupId($groupId);

    /**
     * Get company role id
     *
     * @return int
     */
    public function getCompanyRoleId();

    /**
     * Set company role id
     *
     * @param int $roleId
     * @return $this
     */
    public function setCompanyRoleId($roleId);

     /**
      * Get company unit id
      *
      * @return int
      */
    public function getCompanyUnitId();

    /**
     * Set company unit id
     *
     * @param int $unitId
     * @return $this
     */
    public function setCompanyUnitId(?int $unitId);

    /**
     * Get job title
     *
     * @return string
     */
    public function getJobTitle();

    /**
     * Set job title
     *
     * @param string $jobTitle
     * @return $this
     */
    public function setJobTitle($jobTitle);

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone();

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone);

    /**
     * Get additional info
     *
     * @return string
     */
    public function getAdditionalInfo();

    /**
     * Set additional info
     *
     * @param string $additionalInfo
     * @return $this
     */
    public function setAdditionalInfo($additionalInfo);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ca\Api\Data\CompanyUserExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyUserExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ca\Api\Data\CompanyUserExtensionInterface $extensionAttributes
    );
}
