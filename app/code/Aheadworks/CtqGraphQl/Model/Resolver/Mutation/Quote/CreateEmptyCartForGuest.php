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

use Aheadworks\Ctq\Model\Service\BuyerPermissionService;
use Aheadworks\Ctq\Api\QuoteListManagementInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface;

class CreateEmptyCartForGuest
{
    /**
     * @param QuoteListManagementInterface $quoteListManagement
     * @param QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId
     * @param BuyerPermissionService $buyerPermissionService
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        private readonly QuoteListManagementInterface $quoteListManagement,
        private readonly QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId,
        private readonly BuyerPermissionService $buyerPermissionService,
        private readonly CartRepositoryInterface $cartRepository
    ) {
    }

    /**
     * Create empty cart for guest
     *
     * @param int $customerGroupId
     * @param int $storeId
     * @return string
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     * @throws GraphQlInputException
     */
    public function execute(int $customerGroupId, int $storeId): string
    {
        if (!$this->buyerPermissionService->isAllowQuoteList($customerGroupId, $storeId)) {
            throw new GraphQlInputException(__('Cannot create cart on store for guest'));
        }
        $quoteId = $this->quoteListManagement->createQuoteList();
        $quote = $this->quoteListManagement->getQuoteList($quoteId);
        $quote->setCustomerGroupId($customerGroupId);
        $this->cartRepository->save($quote);

        return (string)$this->quoteIdToMaskedQuoteId->execute((int)$quote->getId());
    }
}
