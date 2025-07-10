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

namespace Aheadworks\Ca\Api;

interface UnitManagementInterface
{
    /**
     * Create default unit
     *
     * @param int $companyId
     * @return \Aheadworks\Ca\Api\Data\UnitInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createDefaultUnit(int $companyId):\Aheadworks\Ca\Api\Data\UnitInterface;

    /**
     * Move Unit
     *
     * @param string[] $unitsData
     * @return void
     */
    public function moveUnit(array $unitsData);
}
