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
namespace Aheadworks\Ca\Plugin\Model\ResourceModel\Grid\CompanyColumn;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class AwNet30CollectionPlugin
 *
 * @package Aheadworks\Ca\Plugin\Model\ResourceModel\Grid\CompanyColumn
 */
class AwNet30CollectionPlugin extends BaseCollectionPlugin
{
    /**
     * Add additional company columns
     *
     * @param AbstractCollection $subject
     * @param array $resultColumns
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetColumns($subject, $resultColumns)
    {
        $resultColumns = array_merge($resultColumns, $this->collection->getAdditionalColumns());
        return $resultColumns;
    }
}
