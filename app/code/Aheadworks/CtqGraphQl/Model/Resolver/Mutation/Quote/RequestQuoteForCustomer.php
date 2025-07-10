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

namespace Aheadworks\CtqGraphQl\Model\Resolver\Mutation\Quote;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Service\BuyerQuoteService;
use Aheadworks\Ctq\Api\Data\RequestQuoteInputInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class RequestQuoteForCustomer
{
    /**
     * @param BuyerQuoteService $buyerQuoteService
     */
    public function __construct(
        private readonly BuyerQuoteService $buyerQuoteService
    ) {
    }

    /**
     * Request quote
     *
     * @param int $cartId
     * @param RequestQuoteInputInterface $requestInput
     * @return QuoteInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(int $cartId, RequestQuoteInputInterface $requestInput): QuoteInterface
    {
        $quote = $this->buyerQuoteService->requestQuoteByRequest($cartId, $requestInput);

        return $quote;
    }
}
