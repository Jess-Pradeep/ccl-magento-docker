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
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action as BackendAction;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Model\Service\SellerQuoteService;
use Aheadworks\Ctq\Model\Quote\Admin\UpdateProcessor;

class Edit extends BackendAction
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Aheadworks_Ctq::quotes';

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = ['edit'];

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param QuoteSession $quoteSession
     * @param QuoteRepositoryInterface $quoteRepository
     * @param SellerQuoteService $sellerQuoteManagement
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        protected readonly PageFactory $resultPageFactory,
        protected readonly QuoteSession $quoteSession,
        protected readonly QuoteRepositoryInterface $quoteRepository,
        protected readonly SellerQuoteService $sellerQuoteManagement,
        protected readonly DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return ResultPage|Redirect
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @var ResultPage $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $quoteId = $this->_request->getParam('id', null);
        if ($quoteId) {
            $this->quoteSession->setConfigKey($quoteId);
        }
        $isQuoteSavedOnReload = (bool)$this->_request->getParam('saveOnReload', false);
        if (!$isQuoteSavedOnReload) {
            $this->quoteSession->clearStorage();
            $this->dataPersistor->clear(UpdateProcessor::DATA_PERSISTOR_FORM_DATA_KEY);
        }
        if (!$isQuoteSavedOnReload && $quoteId) {
            try {
                $cart = $this->sellerQuoteManagement->getCartByQuote($quoteId);
                if ($cart->getAwCtqQuoteIsChanged()) {
                    $this->messageManager->addNoticeMessage(
                        __('This Quote has been updated for some reasons. All details are in the History Log.')
                    );
                }
                $quote = $this->quoteRepository->get($quoteId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addException($exception, __('This quote doesn\'t exist.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
            $this->quoteSession
                ->setQuote($cart)
                ->setCustomerId($quote->getCustomerId())
                ->setStoreId($cart->getStoreId() ?: $quote->getStoreId())
                ->setCurrencyId($cart->getQuoteCurrencyCode())
                ->setQuoteId($cart->getId())
                ->setIsGuestQuote(true);
        }

        $resultPage->setActiveMenu('Aheadworks_Ctq::quotes');
        $resultPage->getConfig()->getTitle()->prepend(__('Quotes'));
        return $resultPage;
    }
}
