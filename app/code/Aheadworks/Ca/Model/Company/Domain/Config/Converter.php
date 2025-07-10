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
namespace Aheadworks\Ca\Model\Company\Domain\Config;

use Magento\Framework\Config\ConverterInterface;

/**
 * Class Converter
 *
 * @package Aheadworks\Ca\Model\Company\Domain\Config
 */
class Converter implements ConverterInterface
{
    /**
     * @inheritdoc
     */
    public function convert($source)
    {
        $output = [];

        /** @var $domain \DOMElement */
        foreach ($source->getElementsByTagName('domain') as $domain) {
            $output[] = $domain->getAttribute('name');
        }

        return $output;
    }
}
