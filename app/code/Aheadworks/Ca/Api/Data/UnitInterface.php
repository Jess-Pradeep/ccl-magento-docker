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

interface UnitInterface
{
    public const PARENT_ID = 'parent_id';
    public const UNIT_TITLE = 'unit_title';
    public const UNIT_ID = 'id';
    public const COMPANY_ID = 'company_id';
    public const UNIT_DESCRIPTION = 'unit_description';
    public const SORT_ORDER = 'sort_order';
    public const PATH = 'path';

    /**
     * Get Unit Id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Unit Id
     *
     * @param int $unitId
     * @return \Aheadworks\Ca\Unit\Api\Data\UnitInterface
     */
    public function setId($unitId);

    /**
     * Get company id
     *
     * @return int|null
     */
    public function getCompanyId();

    /**
     * Set company id
     *
     * @param int $companyId
     * @return \Aheadworks\Ca\Unit\Api\Data\UnitInterface
     */
    public function setCompanyId($companyId);

    /**
     * Get parent id
     *
     * @return int|null
     */
    public function getParentId();

    /**
     * Set parent id
     *
     * @param int $parentId
     * @return \Aheadworks\Ca\Unit\Api\Data\UnitInterface
     */
    public function setParentId($parentId);

    /**
     * Get unit title
     *
     * @return string|null
     */
    public function getUnitTitle();

    /**
     * Set unit title
     *
     * @param string $unitTitle
     * @return \Aheadworks\Ca\Unit\Api\Data\UnitInterface
     */
    public function setUnitTitle($unitTitle);

    /**
     * Get Sort Order
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Set Sort Order
     *
     * @param int $sortOrder
     * @return \Aheadworks\Ca\Unit\Api\Data\UnitInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * Get unit description
     *
     * @return string|null
     */
    public function getUnitDescription();

    /**
     * Get path
     *
     * @return string
     */
    public function getPath();

    /**
     * Set path
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path);

    /**
     * Set unit description
     *
     * @param string $unitDescription
     * @return \Aheadworks\Ca\Unit\Api\Data\UnitInterface
     */
    public function setUnitDescription($unitDescription);
}
