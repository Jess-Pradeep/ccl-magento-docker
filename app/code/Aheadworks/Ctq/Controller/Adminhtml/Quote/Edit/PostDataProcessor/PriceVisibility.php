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
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor;

use Aheadworks\Ctq\Api\Data\QuoteInterface;

class PriceVisibility implements ProcessorInterface
{
    /**
     * Prepare price visibility value
     *
     * @param array $data
     * @return array
     */
    public function process($data)
    {
        if (isset($data['quote']['is_price_managed'])) {
            $data['quote'][QuoteInterface::IS_PRICE_VISIBLE] = $data['quote'][QuoteInterface::IS_PRICE_VISIBLE] ?? '0';
            unset($data['quote']['is_price_managed']);
        }

        return $data;
    }
}
