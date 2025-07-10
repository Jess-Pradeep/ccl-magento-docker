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
namespace Aheadworks\Ctq\Controller\Quote\External;

use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Quote\Cart\ItemsChecker;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ctq\Controller\ExternalAction;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Edit
 *
 * @package Aheadworks\Ctq\Controller\Quote\External
 */
class Edit extends ExternalAction
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ItemsChecker $itemsChecker,
        QuoteRepositoryInterface $quoteRepository
    ) {
        parent::__construct($context, $itemsChecker, $quoteRepository);
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute()
    {
        try {
            $quote = $this->getQuoteByHash();

            $stores = $this->storeManager->getStores();

            if (!isset($stores[$quote->getStoreId()])) {
                $this->messageManager->addNoticeMessage(__('Sorry, the quote is no more available, please contact the merchant for further details.'));
                /** ResultRedirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setRefererOrBaseUrl();
            }
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage(__('This quote doesn\'t exist'));
            /** ResultRedirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Quote %1', $quote->getName()));

        return $resultPage;
    }
}
