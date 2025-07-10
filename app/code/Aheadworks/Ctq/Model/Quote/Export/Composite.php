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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Quote\Export;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\ViewModel\Customer\Quote\Locator;
use Magento\Framework\App\ResponseInterface;

/**
 * Class Composite
 * @package Aheadworks\Ctq\Model\Quote\Export
 */
class Composite
{
    /**
     * @var array
     */
    private $exporters = [];

    /**
     * @param array $exporters
     */
    public function __construct(
        array $exporters = []
    ) {
        $this->exporters = $exporters;
    }

    /**
     * Export quote
     *
     * @param QuoteInterface $quote
     * @param string $type
     * @param string $methodToLocate
     * @return ResponseInterface
     * @throws \Exception
     */
    public function exportQuote($quote, $type, $methodToLocate = Locator::LOCATE_BY_ID)
    {
        $exporter = isset($this->exporters[$type]) ? $this->exporters[$type] : null;

        if ($exporter instanceof ExporterInterface) {
            return $exporter->exportQuote($quote, $methodToLocate);
        }
        
        throw new \Exception(sprintf('Unknown file type: %s requested', $type));
    }
}
