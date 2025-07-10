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

namespace Aheadworks\Ca\Model\ResourceModel\Company\Relation\ShippingMethods;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\ResourceModel\Company;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var MetadataPool
     */
    private MetadataPool $metadataPool;

    /**
     * @var string
     */
    private string $tableName;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->tableName = $this->resourceConnection->getTableName(Company::COMPANY_SHIPPING_METHODS_TABLE_NAME);
    }

    /**
     * Perform action on relation/extension attribute
     *
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = []): object
    {
        /** @var CompanyInterface $entity */
        $entityId = (int)$entity->getId();
        if (!$entityId) {
            return $entity;
        }

        $this->deleteByEntity($entityId);
        $toInsert = $this->getAllowedShippingMethods($entity);
        $this->insertAllowedShippingMethods($toInsert);

        return $entity;
    }

    /**
     * Remove shipping codes by company ID
     *
     * @param int $companyId
     * @return void
     * @throws \Exception
     */
    private function deleteByEntity(int $companyId): void
    {
        $this->getConnection()->delete($this->tableName, ['company_id = ?' => $companyId]);
    }

    /**
     * Retrieve array of shipping data to insert
     *
     * @param CompanyInterface $entity
     * @return array
     */
    private function getAllowedShippingMethods(CompanyInterface $entity): array
    {
        $allowedShippingMethods = [];
        $shippingCodes = $entity->getAllowedShippingMethods();
        foreach ($shippingCodes as $shippingCode) {
            $allowedShippingMethods[] = [
                'company_id' => (int)$entity->getId(),
                'shipping_name' => $shippingCode
            ];
        }

        return $allowedShippingMethods;
    }

    /**
     * Insert allowed shipping methods
     *
     * @param array $toInsert
     * @return void
     * @throws \Exception
     */
    private function insertAllowedShippingMethods(array $toInsert): void
    {
        if (!empty($toInsert)) {
            $this->getConnection()->insertMultiple($this->tableName, $toInsert);
        }
    }

    /**
     * Get connection
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function getConnection(): AdapterInterface
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(CompanyInterface::class)->getEntityConnectionName()
        );
    }
}
