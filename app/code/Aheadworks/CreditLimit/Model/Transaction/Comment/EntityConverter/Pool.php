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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter;

/**
 * Class Pool
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Comment\EntityConverter
 */
class Pool
{
    /**
     * @var ConverterInterface[]
     */
    private $converters;

    /**
     * @param array $converters
     */
    public function __construct(
        $converters = []
    ) {
        $this->converters = $converters;
    }

    /**
     * Get converter by object type
     *
     * @param string $objectType
     * @return ConverterInterface|null
     */
    public function getConverter($objectType)
    {
        if (isset($this->converters[$objectType])) {
            return $this->converters[$objectType];
        }

        return null;
    }
}
