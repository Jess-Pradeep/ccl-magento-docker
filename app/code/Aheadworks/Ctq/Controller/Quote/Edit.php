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
namespace Aheadworks\Ctq\Controller\Quote;

use Aheadworks\Ctq\Controller\BuyerAction;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\Cart\ItemsChecker;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class Edit
 *
 * @package Aheadworks\Ctq\Controller\Quote
 */
class Edit extends BuyerAction
{
    /**
     * {@inheritdoc}
     */
    const IS_QUOTE_BELONGS_TO_CUSTOMER = true;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var BuyerQuoteManagementInterface
     */
    private $buyerQuoteManagement;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param QuoteRepositoryInterface $quoteRepository
     * @param StoreManagerInterface $storeManager
     * @param ItemsChecker $itemsChecker
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        QuoteRepositoryInterface $quoteRepository,
        StoreManagerInterface $storeManager,
        ItemsChecker $itemsChecker,
        PageFactory $resultPageFactory
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $itemsChecker,
            $quoteRepository
        );
        $this->buyerQuoteManagement = $buyerQuoteManagement;
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        $cart = $this->getCart();
        if ($cart && $cart->getAwCtqQuoteIsChanged()) {
            $this->messageManager->addNoticeMessage(
                __('This Quote has been updated for some reasons. All details are in the History Log.')
            );
        } elseif ($cart == null) {
            $this->messageManager->addNoticeMessage(
                __('Sorry, the quote is no more available, please contact the merchant for further details.')
            );
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $quote = $this->getQuote();
        $resultPage->getConfig()->getTitle()->set(__('Quote %1', $quote->getName()));
        $this->setBackLink($resultPage);

        return $resultPage;
    }

    /**
     * Retrieve cart
     *
     * @return CartInterface
     */
    public function getCart()
    {
        $cart = null;
        try {
            $quoteId = (int)$this->getRequest()->getParam('quote_id');
            if ($quoteId) {
                $storeId = $this->storeManager->getStore()->getId();
                /** @var CartInterface $cart */
                $cart = $this->buyerQuoteManagement->getCartByQuote($quoteId, $storeId);
            }
        } catch (LocalizedException $e) {
        }

        return $cart;
    }
}
