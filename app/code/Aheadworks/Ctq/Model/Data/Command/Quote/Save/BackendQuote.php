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
declare(strict_types=1);

namespace Aheadworks\Ctq\Model\Data\Command\Quote\Save;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\CustomerManagement;
use Magento\Quote\Model\Quote;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\SellerQuoteManagementInterface;
use Aheadworks\Ctq\Model\Data\CommandInterface;
use Aheadworks\Ctq\Model\ResourceModel\Quote as QuoteResourceModel;
use Aheadworks\Ctq\Model\Cart\Checker as CartChecker;

class BackendQuote implements CommandInterface
{
    /**
     * @param SellerQuoteManagementInterface $sellerQuoteManagement
     * @param CustomerManagement $customerManagement
     * @param CartRepositoryInterface $cartRepository
     * @param QuoteResourceModel $quoteResourceModel
     * @param CartChecker $cartChecker
     */
    public function __construct(
        private readonly SellerQuoteManagementInterface $sellerQuoteManagement,
        private readonly CustomerManagement $customerManagement,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly QuoteResourceModel $quoteResourceModel,
        private readonly CartChecker $cartChecker
    ) {
    }

    /**
     * Save quote from backend
     *
     * @param array $data
     * @return QuoteInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute($data): QuoteInterface
    {
        if (!isset($data['cart'])) {
            throw new \InvalidArgumentException('cart argument is required');
        }
        if (!isset($data['quote'])) {
            throw new \InvalidArgumentException('quote argument is required');
        }
        if (!isset($data['session_cart_id'])) {
            throw new \InvalidArgumentException('session_cart_id argument is required');
        }

        $cartId = $data['session_cart_id'];
        /** @var QuoteInterface $quote */
        $quote = $data['quote'];
        /** @var Quote $cart */
        $cart = $data['cart'];

        if ($quote->getId()) {
            $quote = $this->sellerQuoteManagement->updateQuote($quote);
        } else {
            try {
                $this->quoteResourceModel->beginTransaction();
                if ($this->cartChecker->checkIfCustomerAccountMustBeCreated($cart)) {
                    $this->customerManagement->populateCustomerInfo($cart);
                    $this->cartRepository->save($cart);
                }
                $quote = $this->sellerQuoteManagement->createQuote(
                    $cartId,
                    $quote
                );

                $this->quoteResourceModel->commit();
            } catch (\Exception $e) {
                $this->quoteResourceModel->rollBack();
                throw $e;
            }
        }

        return $quote;
    }
}
