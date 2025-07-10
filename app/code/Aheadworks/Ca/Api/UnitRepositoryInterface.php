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

use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Ca\Api\Data\UnitInterface;
use Magento\Framework\Exception\LocalizedException;

interface UnitRepositoryInterface
{
    /**
     * Save Unit
     *
     * @param UnitInterface $unit
     * @return UnitInterface
     * @throws LocalizedException
     */
    public function save(
        \Aheadworks\Ca\Api\Data\UnitInterface $unit
    );

    /**
     * Retrieve Unit
     *
     * @param int $unitId
     * @return UnitInterface
     * @throws LocalizedException
     */
    public function get($unitId);

    /**
     * Retrieve Company Root Unit Id
     *
     * @param int $companyId
     * @return UnitInterface
     * @throws LocalizedException
     */
    public function getCompanyRootUnit($companyId);

    /**
     * Retrieve Unit matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return UnitSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Unit
     *
     * @param UnitInterface $unit
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        \Aheadworks\Ca\Api\Data\UnitInterface $unit
    );

    /**
     * Delete Unit by ID
     *
     * @param int $unitId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($unitId);
}
