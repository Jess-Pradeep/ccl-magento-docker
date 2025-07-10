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

namespace Aheadworks\Ca\Model\ResourceModel\Company\Grid\ThirdParty;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DB\Select;
use Aheadworks\Ca\Model\ResourceModel\Company;
use Aheadworks\Ca\Model\ResourceModel\CompanyUser;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Magento\Customer\Model\ResourceModel\Grid\Collection as CustomerGridCollection;

/**
 * Class Collection
 */
class Collection
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var string
     */
    private $relativeField;

    /**
     * @var string
     */
    private $mainTable;

    /**
     * @var array
     */
    private $additionalCompanyColumns = [
        'aw_ca_company' => 'awca_company.name'
    ];

    /**
     * @var array
     */
    private $additionalCompanyUserColumns = [
        'aw_ca_status' => 'awca_company_user.status'
    ];

    /**
     * @var array
     */
    private $replacePartsWhere = [];

    /**
     * @var array
     */
    private $possibleAmbiguousColumns = [
        CompanyInterface::NAME,
        CompanyInterface::EMAIL,
        CompanyInterface::TELEPHONE,
        CompanyInterface::CREATED_AT
    ];

    /**
     * @var array
     */
    private $additionalColumns;

    /**
     * @param ProductMetadataInterface $productMetadata
     * @param string $relativeField
     * @param string $mainTable
     * @param array $additionalCompanyColumns
     * @param array $additionalCompanyUserColumns
     * @param array $replacePartsWhere
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        $relativeField = '',
        $mainTable = 'main_table',
        $additionalCompanyColumns = [],
        $additionalCompanyUserColumns = [],
        array $replacePartsWhere = []
    ) {
        $this->productMetadata = $productMetadata;
        $this->relativeField = $relativeField;
        $this->mainTable = $mainTable;
        $this->additionalCompanyColumns = array_merge($this->additionalCompanyColumns, $additionalCompanyColumns);
        $this->additionalCompanyUserColumns = array_merge(
            $this->additionalCompanyUserColumns,
            $additionalCompanyUserColumns
        );
        $this->additionalColumns = array_merge($this->additionalCompanyColumns, $this->additionalCompanyUserColumns);
        $this->possibleAmbiguousColumns = array_merge($this->possibleAmbiguousColumns, [$relativeField]);
        $this->replacePartsWhere = $replacePartsWhere;
    }

    /**
     * Join fields before load
     *
     * @param AbstractDb $collection
     * @return AbstractDb
     * @throws \Zend_Db_Select_Exception
     */
    public function joinFieldsBeforeLoad($collection)
    {
        if (!$collection->isLoaded() && !$this->checkIfFieldsAreAlreadyJoined($collection)) {
            $this->joinFields($collection);
        }

        return $collection;
    }

    /**
     * Join fields
     *
     * @param AbstractDb $collection
     * @return void
     */
    private function joinFields($collection)
    {
        $select = $collection->getSelect();

        $select->joinLeft(
            ['awca_company_user' => $collection->getTable(CompanyUser::MAIN_TABLE_NAME)],
            'awca_company_user.customer_id = ' . $this->mainTable . '.' . $this->relativeField,
            $this->additionalCompanyUserColumns
        );
        $select->joinLeft(
            ['awca_company' => $collection->getTable(Company::MAIN_TABLE_NAME)],
            'awca_company_user.company_id = awca_company.id',
            $this->additionalCompanyColumns
        );

        if ($collection->getSelect()->getPart(Select::WHERE)) {
            $this->preparePartsWhere($collection);
        }

        foreach ($this->additionalColumns as $filter => $alias) {
            $collection->addFilterToMap($filter, $alias);
        }
    }

    /**
     * Prepare parts where
     *
     * @param AbstractDb $collection
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    public function preparePartsWhere(AbstractDb $collection): void
    {
        $wherePart = $collection->getSelect()->getPart(Select::WHERE);
        $result = str_replace($this->replacePartsWhere['search'], $this->replacePartsWhere['replace'], $wherePart);

        if (!empty($result)) {
            $collection->getSelect()->reset(Select::WHERE);
            $collection->getSelect()->setPart(Select::WHERE, $result);
        }
    }

    /**
     * Check if fields are already joined
     *
     * @param AbstractDb $collection
     * @return bool
     * @throws \Zend_Db_Select_Exception
     */
    private function checkIfFieldsAreAlreadyJoined($collection)
    {
        $fromPart = $collection->getSelect()->getPart(Select::FROM);
        foreach ($fromPart as $tableName => $joinConfig) {
            if ($tableName == 'awca_company') {
                return true;
            }
        }

        return false;
    }

    /**
     * Add field to filter for third party collection
     *
     * @param AbstractDb $collection
     * @param string $field
     * @return AbstractDb
     */
    public function addFieldToFilter($collection, $field)
    {
        if (!is_array($field) && isset($this->additionalColumns[$field])) {
            $alias = $this->additionalColumns[$field];
            // Customer collection method AddFieldToFilter has been overridden since 2.3.6
            if ($collection instanceof CustomerGridCollection) {
                $field = $this->isMainTablePrefixRequired()
                    ? $this->mainTable . '.' . $field
                    : $field;
            }
            $collection->addFilterToMap($field, $alias);
        }
        if (in_array($field, $this->possibleAmbiguousColumns)) {
            $collection->addFilterToMap($field, $this->mainTable . '.' . $field);
        }

        return $collection;
    }

    /**
     * Get all additional columns
     *
     * @return array
     */
    public function getAdditionalColumns()
    {
        return array_merge(
            $this->additionalColumns,
            $this->additionalCompanyColumns,
            $this->additionalCompanyUserColumns
        );
    }

    /**
     * Is main table prefix required
     *
     * @return bool
     */
    private function isMainTablePrefixRequired()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        return version_compare($magentoVersion, '2.3.6', '>=')
            && version_compare($magentoVersion, '2.4.0', '!=');
    }
}
