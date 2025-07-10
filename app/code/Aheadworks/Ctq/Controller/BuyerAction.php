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
namespace Aheadworks\Ctq\Controller;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\Cart\ItemsChecker;
use Aheadworks\Ctq\Model\Quote\QuoteManagement;
use Magento\Framework\View\Result\Page;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class BuyerAction
 *
 * @package Aheadworks\Ctq\Controller
 */
abstract class BuyerAction extends AbstractAction
{
    /**
     * Check if quote belongs to customer
     */
    const IS_QUOTE_BELONGS_TO_CUSTOMER = false;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var QuoteRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ItemsChecker $itemsChecker
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        ItemsChecker $itemsChecker,
        QuoteRepositoryInterface $quoteRepository
    ) {
        parent::__construct($context, $itemsChecker);
        $this->customerSession = $customerSession;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws LocalizedException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        } elseif (static::IS_QUOTE_BELONGS_TO_CUSTOMER
            && !$this->isQuoteBelongsToCustomer()
        ) {
            throw new NotFoundException(__('Page not found.'));
        }

        return parent::dispatch($request);
    }

    /**
     * Retrieve quote
     *
     * @return QuoteInterface
     * @throws NotFoundException
     */
    protected function getQuote()
    {
        try {
            $quoteId = (int)$this->getRequest()->getParam('quote_id');
            $requestEntity = $this->quoteRepository->get($quoteId);
        } catch (NoSuchEntityException $e) {
            throw new NotFoundException(__('Page not found.'));
        }

        return $requestEntity;
    }

    /**
     * Check if quote belongs to current customer
     *
     * @return bool
     * @throws NotFoundException
     */
    protected function isQuoteBelongsToCustomer()
    {
        $quote = $this->getQuote();
        if ($quote->getId()
            && $quote->getCustomerId() == $this->customerSession->getCustomerId()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Set back link
     *
     * @param Page $resultPage
     * @param bool $backUrl
     * @return void
     */
    protected function setBackLink($resultPage, $backUrl = null)
    {
        $backUrl = $backUrl ? : $this->_redirect->getRefererUrl();
        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($backUrl);
        }
    }
}
