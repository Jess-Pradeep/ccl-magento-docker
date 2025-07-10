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

interface CompanySharedAddressManagementInterface
{
    /**
     * Set is address list shared to all users
     *
     * @param bool $isEnabled
     * @param int $companyId
     * @return void
     */
    public function setIsAddressListSharedToAllUsers(bool $isEnabled, int $companyId): void;

    /**
     * Copy company admin addresses to all company users
     *
     * @param int $companyId
     * @return void
     */
    public function copyCompanyAdminAddressListToAllUsers(int $companyId): void;

    /**
     * Update company admin address to all company users
     *
     * @param int $companyId
     * @param int $rootAddressId
     * @return void
     */
    public function updateCompanyAdminAddressToAllUsers(int $companyId, int $rootAddressId): void;

    /**
     * Delete shared addresses
     *
     * @param int $companyId
     * @param bool $forRootAddressIsNull
     * @return void
     */
    public function deleteSharedAddresses(int $companyId, bool $forRootAddressIsNull = false): void;

    /**
     * Add shared addresses to new company user
     *
     * @param int $companyId
     * @param int $companyUserId
     * @return void
     */
    public function addSharedAddressesToNewCompanyUser(int $companyId, int $companyUserId): void;

    /**
     * Delete shared addresses to company user
     *
     * @param int $companyId
     * @param int $companyUserId
     * @return void
     */
    public function deleteSharedAddressesToCompanyUser(int $companyId, int $companyUserId): void;
}
