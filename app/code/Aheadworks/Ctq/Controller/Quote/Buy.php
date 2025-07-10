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

use Aheadworks\Ctq\Model\Quote\Cart\ItemsChecker;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ctq\Model\Data\CommandInterface;
use Aheadworks\Ctq\Controller\BuyerAction;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Buy
 *
 * @package Aheadworks\Ctq\Controller\Quote
 */
class Buy extends BuyerAction
{
    /**
     * {@inheritdoc}
     */
    const IS_QUOTE_BELONGS_TO_CUSTOMER = true;

    /**
     * @var CommandInterface
     */
    private $buyCommand;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param QuoteRepositoryInterface $quoteRepository
     * @param ItemsChecker $itemsChecker
     * @param StoreManagerInterface $storeManager
     * @param CommandInterface $buyCommand
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        QuoteRepositoryInterface $quoteRepository,
        ItemsChecker $itemsChecker,
        StoreManagerInterface $storeManager,
        CommandInterface $buyCommand
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $itemsChecker,
            $quoteRepository
        );
        $this->buyCommand = $buyCommand;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $quote = $this->getQuote();
            if (!$this->isQuoteCanBeEdited($quote)) {
                return $resultRedirect->setPath('*/*/');
            }
            $data = [
                'store_id' => $this->storeManager->getStore()->getId(),
                'quote_id' => $quote->getId()
            ];
            $this->buyCommand->execute($data);
            return $this->redirectTo($resultRedirect);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while save the quote.')
            );
        }
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }

    /**
     * @inheritdoc
     */
    protected function redirectTo($resultRedirect)
    {
        return $resultRedirect->setPath('checkout');
    }
}
