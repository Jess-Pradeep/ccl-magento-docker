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
namespace Aheadworks\Ca\Model\Customer;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Aheadworks\Ca\Model\Customer\CompanyUser\EntityProcessor;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser as CompanyUserResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class CompanyUser extends AbstractModel implements CompanyUserInterface
{
    /**
     * @var EntityProcessor
     */
    private $processor;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param EntityProcessor $processor
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @throws LocalizedException
     */
    public function __construct(
        Context $context,
        Registry $registry,
        EntityProcessor $processor,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->processor = $processor;
    }

    /**
     * Construct for int class
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CompanyUserResourceModel::class);
    }

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get company id
     *
     * @return int
     */
    public function getCompanyId()
    {
        return $this->getData(self::COMPANY_ID);
    }

    /**
     * Set company id
     *
     * @param int $companyId
     * @return $this
     */
    public function setCompanyId($companyId)
    {
        return $this->setData(self::COMPANY_ID, $companyId);
    }

    /**
     * Get is root flag
     *
     * @return boolean
     */
    public function getIsRoot()
    {
        return $this->getData(self::IS_ROOT);
    }

    /**
     * Set is root flag
     *
     * @param boolean $isRoot
     * @return $this
     */
    public function setIsRoot($isRoot)
    {
        return $this->setData(self::IS_ROOT, $isRoot);
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get company group id
     *
     * @return int
     */
    public function getCompanyGroupId()
    {
        return $this->getData(self::COMPANY_GROUP_ID);
    }

    /**
     * Set company group id
     *
     * @param int $groupId
     * @return $this
     */
    public function setCompanyGroupId($groupId)
    {
        return $this->setData(self::COMPANY_GROUP_ID, $groupId);
    }

    /**
     * Get company role id
     *
     * @return int
     */
    public function getCompanyRoleId()
    {
        return $this->getData(self::COMPANY_ROLE_ID);
    }

    /**
     * Set company role id
     *
     * @param int $roleId
     * @return $this
     */
    public function setCompanyRoleId($roleId)
    {
        return $this->setData(self::COMPANY_ROLE_ID, $roleId);
    }

    /**
     * Get company unit id
     *
     * @return int
     */
    public function getCompanyUnitId()
    {
        return $this->getData(self::COMPANY_UNIT_ID);
    }

     /**
      * Set company unit id
      *
      * @param int $unitId
      * @return \Aheadworks\Ca\Api\Data\CompanyUserInterface
      */
    public function setCompanyUnitId(?int $unitId)
    {
        return $this->setData(self::COMPANY_UNIT_ID, $unitId);
    }

    /**
     * Get job title
     *
     * @return string
     */
    public function getJobTitle()
    {
        return $this->getData(self::JOB_TITLE);
    }

    /**
     * Set job title
     *
     * @param string $jobTitle
     * @return $this
     */
    public function setJobTitle($jobTitle)
    {
        return $this->setData(self::JOB_TITLE, $jobTitle);
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->getData(self::TELEPHONE);
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * Get additional info
     *
     * @return string
     */
    public function getAdditionalInfo()
    {
        return $this->getData(self::ADDITIONAL_INFO);
    }

    /**
     * Set additional info
     *
     * @param string $additionalInfo
     * @return $this
     */
    public function setAdditionalInfo($additionalInfo)
    {
        return $this->setData(self::ADDITIONAL_INFO, $additionalInfo);
    }

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Ca\Api\Data\CompanyUserExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyUserExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Aheadworks\Ca\Api\Data\CompanyUserExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * Processing object before save data
     *
     * @return $this
     */
    public function beforeSave()
    {
        $this->processor->prepareDataBeforeSave($this);
        return parent::beforeSave();
    }

    /**
     * Processing object after load data
     *
     * @return $this
     */
    public function afterLoad()
    {
        $this->processor->prepareDataAfterLoad($this);
        return $this;
    }
}
