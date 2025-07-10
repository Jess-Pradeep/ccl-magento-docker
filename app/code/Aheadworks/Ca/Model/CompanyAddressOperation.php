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

use Aheadworks\Ca\Api\Data\CompanyAddressOperationInterface;
use Magento\Framework\Model\AbstractModel;

class CompanyAddressOperation extends AbstractModel implements CompanyAddressOperationInterface
{
    /**
     * Get company id
     *
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->getData(self::COMPANY_ID);
    }

    /**
     * Set company id
     *
     * @param int $companyId
     * @return self
     */
    public function setCompanyId(int $companyId): self
    {
        return $this->setData(self::COMPANY_ID, $companyId);
    }

    /**
     * Get address id
     *
     * @return null|int
     */
    public function getAddressId(): ?int
    {
        return $this->getData(self::ADDRESS_ID);
    }

    /**
     * Set address id
     *
     * @param int $addressId
     * @return self
     */
    public function setAddressId(int $addressId): self
    {
        return $this->setData(self::ADDRESS_ID, $addressId);
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction(): ?string
    {
        return $this->getData(self::ACTION);
    }

    /**
     * Set action
     *
     * @param string $action
     * @return self
     */
    public function setAction(string $action): self
    {
        return $this->setData(self::ACTION, $action);
    }
}
