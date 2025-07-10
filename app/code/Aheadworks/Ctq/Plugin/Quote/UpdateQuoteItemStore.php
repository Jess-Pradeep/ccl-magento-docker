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
namespace Aheadworks\Ctq\Plugin\Quote;

use Magento\Checkout\Model\Session;
use Magento\Quote\Model\QuoteRepository;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreSwitcherInterface;

class UpdateQuoteItemStore
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @param QuoteRepository $quoteRepository
     * @param Session $checkoutSession
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        Session $checkoutSession
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Update store id in active quote after store view switching.
     *
     * @param StoreSwitcherInterface $subject
     * @param string $result
     * @param StoreInterface $fromStore store where we came from
     * @param StoreInterface $targetStore store where to go to
     * @param string $redirectUrl original url requested for redirect after switching
     * @return string url to be redirected after switching
     */
    public function afterSwitch(
        StoreSwitcherInterface $subject,
        $result,
        StoreInterface $fromStore,
        StoreInterface $targetStore,
        string $redirectUrl
    ): string {
        $quoteId = $this->checkoutSession->getAwCtqQuoteListId();

        if ($quoteId) {
            try {
                $quote = $this->quoteRepository->get($quoteId);
                if ($quote->getIsActive()) {
                    $quote->setStoreId(
                        $targetStore->getId()
                    );
                    $quote->getItemsCollection(false);
                    $this->quoteRepository->save($quote);
                }
            } catch (\Exception $e) {
                // We continue without updating
            }
        }

        return $result;
    }
}