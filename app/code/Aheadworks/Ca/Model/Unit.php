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

use Aheadworks\Ca\Api\Data\UnitInterface;
use Magento\Framework\Model\AbstractModel;

class Unit extends AbstractModel implements UnitInterface
{
    /**
     * Unit Construct
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Aheadworks\Ca\Model\ResourceModel\Unit::class);
    }

    /**
     * Get Unit Id
     *
     * @return int|null
     */
    public function getId() :?string
    {
        return $this->getData(self::UNIT_ID);
    }

    /**
     * Set Unit Id
     *
     * @param int $unitId
     * @return UnitInterface
     */
    public function setId($unitId):UnitInterface
    {
        return $this->setData(self::UNIT_ID, $unitId);
    }

    /**
     * Get company id
     *
     * @return int|null
     */
    public function getCompanyId()
    {
        return $this->getData(self::COMPANY_ID);
    }

    /**
     * Set company id
     *
     * @param int $companyId
     * @return UnitInterface
     */
    public function setCompanyId($companyId):UnitInterface
    {
        return $this->setData(self::COMPANY_ID, $companyId);
    }

    /**
     * Get parent id
     *
     * @return int|null
     */
    public function getParentId()
    {
        return $this->getData(self::PARENT_ID);
    }

    /**
     * Set parent id
     *
     * @param int $parentId
     * @return UnitInterface
     */
    public function setParentId($parentId):UnitInterface
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    /**
     * Get unit title
     *
     * @return string|null
     */
    public function getUnitTitle()
    {
        return $this->getData(self::UNIT_TITLE);
    }

    /**
     * Set unit title
     *
     * @param string $unitTitle
     * @return UnitInterface
     */
    public function setUnitTitle($unitTitle):UnitInterface
    {
        return $this->setData(self::UNIT_TITLE, $unitTitle);
    }

    /**
     * Get Sort Order
     *
     * @return int|null
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Set Sort Order
     *
     * @param int $sortOrder
     * @return UnitInterface
     */
    public function setSortOrder($sortOrder):UnitInterface
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Get unit description
     *
     * @return string|null
     */
    public function getUnitDescription()
    {
        return $this->getData(self::UNIT_DESCRIPTION);
    }

    /**
     * Set unit description
     *
     * @param string $unitDescription
     * @return UnitInterface
     */
    public function setUnitDescription($unitDescription):UnitInterface
    {
        return $this->setData(self::UNIT_DESCRIPTION, $unitDescription);
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getData(self::PATH);
    }

    /**
     * Set path
     *
     * @param string $path
     * @return UnitInterface
     */
    public function setPath($path):UnitInterface
    {
        return $this->setData(self::PATH, $path);
    }
}
