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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options;

use Magento\Catalog\Model\ProductOptionProcessorInterface;

/**
 * Class ProcessorPool
 * @package Aheadworks\RequisitionLists\Model\RequisitionList\Item\Options
 */
class ProcessorPool
{
    /**
     * @var ProductOptionProcessorInterface[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(
        array $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Retrieve product option processor
     *
     * @param string $type
     * @return ProductOptionProcessorInterface|null
     */
    public function get($type)
    {
        $processor = null;
        if (isset($this->processors[$type])) {
            $processor = $this->processors[$type];
        }

        return $processor;
    }
}
