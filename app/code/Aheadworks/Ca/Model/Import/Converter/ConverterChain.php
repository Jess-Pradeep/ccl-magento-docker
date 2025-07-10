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

namespace Aheadworks\Ca\Model\Import\Converter;

use Magento\Framework\Exception\LocalizedException;

/**
 * Composite converter
 */
class ConverterChain implements ConverterInterface
{
    /**
     * @var ConverterInterface[]
     */
    private array $converters;

    /**
     * @param array $converters
     * @throws LocalizedException
     */
    public function __construct(
        array $converters = []
    ) {
        foreach ($converters as $converter) {
            if (!$converter instanceof ConverterInterface) {
                throw new LocalizedException(
                    __('Import row converter must implement %1.', ConverterInterface::class)
                );
            }
        }
        $this->converters = $converters;
    }

    /**
     * Convert csv data row to entity data row
     *
     * @param array $importDataRow
     * @param array $resultDataRow
     * @return array
     */
    public function convert(array $importDataRow, array $resultDataRow): array
    {
        foreach ($this->converters as $converter) {
            $resultDataRow = $converter->convert($importDataRow, $resultDataRow);
        }

        return $resultDataRow;
    }
}
