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
namespace Aheadworks\Ctq\Plugin\Model\Quote\QuoteRepository;

use Aheadworks\Ctq\Model\Cart\ExtensionAttributes\JoinAttributeProcessor;
use Magento\Quote\Model\QuoteRepository\LoadHandler;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class LoadHandlerPlugin
 * @package Aheadworks\Ctq\Plugin\Model\Quote\QuoteRepository
 */
class LoadHandlerPlugin
{
    /**
     * @var JoinAttributeProcessor
     */
    private $joinAttributeProcessor;

    /**
     * @param JoinAttributeProcessor $joinAttributeProcessor
     */
    public function __construct(
        JoinAttributeProcessor $joinAttributeProcessor
    ) {
        $this->joinAttributeProcessor = $joinAttributeProcessor;
    }

    /**
     * Attach quote data to cart extension attribute
     *
     * @param LoadHandler $subject
     * @param CartInterface $quote
     * @return CartInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterLoad($subject, CartInterface $quote)
    {
        $quote = $this->joinAttributeProcessor->process($quote);

        return $quote;
    }
}
