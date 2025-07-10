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
 * @package    CtqGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CtqGraphQl\Model;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\CtqGraphQl\Model\DataProcessor\Pool as ProcessorsPool;
use Magento\Framework\Reflection\DataObjectProcessor;

class ObjectConverter
{
    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param ProcessorsPool $processorsPool
     */
    public function __construct(
        private readonly DataObjectProcessor $dataObjectProcessor,
        private readonly ProcessorsPool $processorsPool
    ) {
    }

    /**
     * Convert object to array with processing
     *
     * @param QuoteInterface|QuoteInterface[] $object
     * @param string $instanceName
     * @return array
     */
    public function convertToArray($object, string $instanceName): array
    {
        if (is_array($object)) {
            foreach ($object as $key => $quote) {
                $data[$key] = $this->dataObjectProcessor->buildOutputDataArray(
                    $quote,
                    $instanceName
                );
                $data[$key]['model'] = $quote;
            }
        } else {
            $data = $this->dataObjectProcessor->buildOutputDataArray(
                $object,
                $instanceName
            );
            $data['model'] = $object;
        }

        $dataArrayProcessor = $this->processorsPool->getForInstance($instanceName);
        if ($dataArrayProcessor) {
            $data = $dataArrayProcessor->process($data);
        }

        return $data;
    }
}
