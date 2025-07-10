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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Filter aggregator
 */
class FilterProcessorAggregator
{
    /**
     * @var FilterProcessorInterface[]
     */
    private array $handler;

    /**
     * @param FilterProcessorInterface[] $handler
     * @throws LocalizedException
     */
    public function __construct(array $handler = [])
    {
        foreach ($handler as $filterProcessor) {
            if (!($filterProcessor instanceof FilterProcessorInterface)) {
                throw new LocalizedException(__(
                    'Filter handler must be instance of "%interface"',
                    ['interface' => FilterProcessorInterface::class]
                ));
            }
        }

        $this->handler = $handler;
    }

    /**
     * Process
     *
     * @param string $type
     * @param AbstractCollection $collection
     * @param string $filterName
     * @param string|array $filterValue
     * @throws LocalizedException
     */
    public function process(
        string $type,
        AbstractCollection $collection,
        string $filterName,
        array|string $filterValue
    ): void {
        if (!isset($this->handler[$type])) {
            throw new LocalizedException(__(
                'No filter processor for "%type" given.',
                ['type' => $type]
            ));
        }
        $this->handler[$type]->process($collection, $filterName, $filterValue);
    }
}
