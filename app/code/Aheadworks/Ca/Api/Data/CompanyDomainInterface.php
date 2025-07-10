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
namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CompanyDomainInterface
 * @api
 */
interface CompanyDomainInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const COMPANY_ID = 'company_id';
    const NAME = 'name';
    const STATUS = 'status';
    const IS_APPROVED_NOTIFICATION_SENT = 'is_approved_notification_sent';
    /**#@-*/

    const REQUESTED_BY = 'requested_by';

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get company ID
     *
     * @return int
     */
    public function getCompanyId();

    /**
     * Set company ID
     *
     * @param int $companyId
     * @return $this
     */
    public function setCompanyId($companyId);

    /**
     * Get domain name
     *
     * @return string
     */
    public function getName();

    /**
     * Set domain name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Set is approved notification sent
     *
     * @param bool $isNotificationSent
     * @return $this
     */
    public function setIsApprovedNotificationSent($isNotificationSent);

    /**
     * Get is approved notification sent
     *
     * @return bool
     */
    public function getIsApprovedNotificationSent();

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Ca\Api\Data\CompanyDomainExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyDomainExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Ca\Api\Data\CompanyDomainExtensionInterface $extensionAttributes
    );
}
