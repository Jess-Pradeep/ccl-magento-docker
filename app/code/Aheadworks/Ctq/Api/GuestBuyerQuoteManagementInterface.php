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
namespace Aheadworks\Ctq\Api;

/**
 * Interface GuestBuyerQuoteManagementInterface
 *
 * @api
 * @package Aheadworks\Ctq\Api
 */
interface GuestBuyerQuoteManagementInterface
{
    /**
     * Request a quote
     *
     * @param string $cartId
     * @param \Aheadworks\Ctq\Api\Data\RequestQuoteInputInterface $requestInput
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function requestQuote($cartId, $requestInput);

    /**
     * Request a quote list
     *
     * @param string $cartId
     * @param \Aheadworks\Ctq\Api\Data\RequestQuoteInputInterface $requestInput
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function requestQuoteList($cartId, $requestInput);
}
