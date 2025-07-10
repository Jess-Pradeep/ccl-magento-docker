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

namespace Aheadworks\Ca\Model\ResourceModel;

use Aheadworks\Ca\Model\CompanySharedAddressInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CompanySharedAddress extends AbstractDb
{
    /**#@+
     * Constants defined for table names
     */
    public const MAIN_TABLE_NAME = 'aw_ca_company_shared_address';
    /**#@-*/

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE_NAME, CompanySharedAddressInterface::ID);
    }

    /**
     * Get exist root address ids
     *
     * @param int $companyId
     * @return array|null
     * @throws LocalizedException
     */
    public function getExistRootAddressIds(int $companyId): ?array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(),CompanySharedAddressInterface::ROOT_ADDRESS_ID)
            ->where(CompanySharedAddressInterface::COMPANY_ID . ' = ?', $companyId)
            ->group(CompanySharedAddressInterface::ROOT_ADDRESS_ID);

        return $connection->fetchCol($select);
    }

    /**
     * Get exist company user address ids
     *
     * @param int $companyId
     * @param int $rootAddressId
     * @return array|null
     * @throws LocalizedException
     */
    public function getExistCompanyUserAddressIds(int $companyId, int $rootAddressId): ?array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(),CompanySharedAddressInterface::COMPANY_USER_ADDRESS_ID)
            ->where(CompanySharedAddressInterface::COMPANY_ID . ' = ?', $companyId)
            ->where(CompanySharedAddressInterface::ROOT_ADDRESS_ID . ' = ?', $rootAddressId)
            ->where(CompanySharedAddressInterface::COMPANY_USER_ADDRESS_ID . ' IS NOT NULL');

        return $connection->fetchCol($select);
    }
}
