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

use Magento\Framework\Data\Collection\AbstractDb;
use Aheadworks\Ca\Model\ResourceModel\Company\Grid\ThirdParty\Collection;

/**
 * Class BaseCollectionPlugin
 *
 * @package Aheadworks\Ca\Plugin\Model\ResourceModel\Grid\CompanyColumn
 */
class BaseCollectionPlugin
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @param Collection $collection
     */
    public function __construct(
        Collection $collection
    ) {
        $this->collection = $collection;
    }

    /**
     * Before load plugin
     *
     * @param AbstractDb $subject
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     */
    public function beforeLoad(
        $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        $this->collection->joinFieldsBeforeLoad($subject);

        return [$printQuery, $logQuery];
    }

    /**
     * Before get size plugin
     *
     * GetSize method can be used without loading collection (e.g. grid export)
     * So we need to join company fields
     *
     * @param AbstractDb $subject
     */
    public function beforeGetSize($subject)
    {
        $this->collection->joinFieldsBeforeLoad($subject);
    }

    /**
     * Around addFieldToFilter plugin
     *
     * @param AbstractDb $subject
     * @param \Closure $proceed
     * @param string|array $field
     * @param array|null $condition
     * @return AbstractDb
     */
    public function aroundAddFieldToFilter(
        $subject,
        \Closure $proceed,
        $field,
        $condition = null
    ) {
        $this->collection->addFieldToFilter($subject, $field);

        return $proceed($field, $condition);
    }
}
