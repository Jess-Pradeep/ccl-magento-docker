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

namespace Aheadworks\Ca\Model\Export;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Magento\Framework\Config\Data as ConfigData;

/**
 * Main export configuration class
 */
class Config extends ConfigData
{
    public const NON_SELECTABLE_FIELDS = [
         CompanyInterface::ALLOWED_SHIPPING_METHODS,
         CompanyInterface::ALLOWED_PAYMENT_METHODS,
        'credit_limit'
    ];
    public const COMPANY_FIELDSET = 'company';
    public const COMPANY_ADMIN_FIELDSET = 'company_admin';
    public const ROOT_USER_FIELDSET = 'root_company_user';

    /**
     * Get filterable attributes
     *
     * @param string $entity
     * @return array
     */
    public function getFilterableAttributes(string $entity): array
    {
        $allAttributes = [];
        if ($entity == ExportEntity::COMPANY_ENTITY_TYPE) {
            $fieldsetList = $this->getFieldsetList($entity);
            $allAttributes = $fieldsetList['company']['attributes'] ?? [];
        }

        return array_filter($allAttributes, fn ($attribute) => $attribute['is_filterable'] === true);
    }

    /**
     * Prepare columns to collection select
     *
     * @param string $entity
     * @param string $fieldsetName
     * @return array
     */
    public function prepareColumnsToSelect(string $entity, string $fieldsetName): array
    {
        $columns = [];
        $fieldsetList = $this->getFieldsetList($entity);
        if (!isset($fieldsetList[$fieldsetName])) {
            return $columns;
        }

        $fieldset = $fieldsetList[$fieldsetName];
        $attributeCodes = $this->retrieveAttributeCodesFromFieldSet($fieldset['attributes'] ?? []);
        foreach ($attributeCodes as $code) {
            if (in_array($code, self::NON_SELECTABLE_FIELDS)) {
                continue;
            }
            $columnName = $this->resolveCode($fieldset['csv_field_prefix'], $code);
            $columns[$columnName] = $fieldset['table_name'] . '.' . $code;
        }

        return $columns;
    }

    /**
     * Prepare headers for csv file
     *
     * @param string $entity
     * @return array
     */
    public function prepareCsvHeaders(string $entity): array
    {
        $headers = [];
        $fieldsetList = $this->getFieldsetList($entity);
        foreach ($fieldsetList as $fieldset) {
            foreach ($fieldset['attributes'] as $attribute) {
                $headers[] = $this->resolveCode($fieldset['csv_field_prefix'], $attribute['code']);
            }
        }

        return $headers;
    }

    /**
     * Prepare columns to csf file
     *
     * @param string $entity
     * @return array
     */
    public function prepareCsvColumns(string $entity): array
    {
        $columns = [];
        $fieldsetList = $this->getFieldsetList($entity);
        foreach ($fieldsetList as $fieldset) {
            foreach ($fieldset['attributes'] as $attribute) {
                $columns[] = [
                    'code' => $this->resolveCode($fieldset['csv_field_prefix'], $attribute['code']),
                    'dataType' => $attribute['dataType']
                ];
            }
        }

        return $columns;
    }

    /**
     * Prepare fieldset fields
     *
     * @param string $entity
     * @param string $fieldSetName
     * @return array
     */
    public function getFieldsetFields(string $entity, string $fieldSetName): array
    {
        $columns = [];
        $fieldsetList = $this->getFieldsetList($entity);
        $attributes = $fieldsetList[$fieldSetName]['attributes'] ?? [];
        foreach ($attributes as $attribute) {
            $columns[] = $this->resolveCode($fieldsetList[$fieldSetName]['csv_field_prefix'], $attribute['code']);
        }

        return $columns;
    }

    /**
     * Get fieldset list
     *
     * @param string $entity
     * @return array
     */
    public function getFieldsetList(string $entity): array
    {
        return $this->get($entity);
    }

    /**
     * Get csv field prefix
     *
     * @param string $entity
     * @param string $fieldsetName
     * @return string
     */
    public function getCsvFieldPrefix(string $entity, string $fieldsetName): string
    {
        $fieldsetList = $this->getFieldsetList($entity);
        return $fieldsetList[$fieldsetName]['csv_field_prefix'] ?? '';
    }

    /**
     * Retrieve attribute codes from field set
     *
     * @param array $fieldset
     * @return array
     */
    private function retrieveAttributeCodesFromFieldSet(array $fieldset): array
    {
        return array_map(fn ($attribute) => $attribute['code'], $fieldset);
    }

    /**
     * Resolver code
     *
     * @param string $prefix
     * @param string $code
     * @return string
     */
    public function resolveCode(string $prefix, string $code): string
    {
        return $prefix ? $prefix . '_' . $code : $code;
    }
}
