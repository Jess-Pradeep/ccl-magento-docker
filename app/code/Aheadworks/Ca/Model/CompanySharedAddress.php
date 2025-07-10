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

namespace Aheadworks\Ca\Model;

use Aheadworks\Ca\Model\ResourceModel\CompanySharedAddress as CompanySharedAddressResourceModel;
use Magento\Framework\Model\AbstractModel;

class CompanySharedAddress extends AbstractModel implements CompanySharedAddressInterface
{
    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(CompanySharedAddressResourceModel::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->hasData(self::ID) ? (int)$this->getData(self::ID) : null;
    }

    /**
     * Set ID
     *
     * @param int|null $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, (int)$id);
    }

    /**
     * Get company id
     *
     * @return int|null
     */
    public function getCompanyId(): ?int
    {
        return $this->hasData(self::COMPANY_ID) ? (int)$this->getData(self::COMPANY_ID) : null;
    }

    /**
     * Set company id
     *
     * @param int $companyId
     * @return $this
     */
    public function setCompanyId(int $companyId): self
    {
        return $this->setData(self::COMPANY_ID, $companyId);
    }

    /**
     * Get root address id
     *
     * @return int|null
     */
    public function getRootAddressId(): ?int
    {
        return $this->hasData(self::ROOT_ADDRESS_ID) ? (int)$this->getData(self::ROOT_ADDRESS_ID) : null;
    }

    /**
     * Set root address id
     *
     * @param int $rootAddressId
     * @return $this
     */
    public function setRootAddressId(int $rootAddressId): self
    {
        return $this->setData(self::ROOT_ADDRESS_ID, $rootAddressId);
    }

    /**
     * Get company user id
     *
     * @return int|null
     */
    public function getCompanyUserId(): ?int
    {
        return $this->hasData(self::COMPANY_USER_ID) ? (int)$this->getData(self::COMPANY_USER_ID) : null;
    }

    /**
     * Set company user id
     *
     * @param int $companyUserId
     * @return $this
     */
    public function setCompanyUserId(int $companyUserId): self
    {
        return $this->setData(self::COMPANY_USER_ID, $companyUserId);
    }

    /**
     * Get company user address id
     *
     * @return int|null
     */
    public function getCompanyUserAddressId(): ?int
    {
        return $this->hasData(self::COMPANY_USER_ADDRESS_ID)
            ? (int)$this->getData(self::COMPANY_USER_ADDRESS_ID)
            : null;
    }

    /**
     * Set company user address id
     *
     * @param int $addressId
     * @return $this
     */
    public function setCompanyUserAddressId(int $addressId): self
    {
        return $this->setData(self::COMPANY_USER_ADDRESS_ID, $addressId);
    }
}
