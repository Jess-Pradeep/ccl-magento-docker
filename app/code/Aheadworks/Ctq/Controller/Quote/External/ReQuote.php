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

use Aheadworks\Ctq\Model\Quote\Cart\ItemsChecker;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Context;
use Aheadworks\Ctq\Controller\ExternalAction;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;

/**
 * Class ReQuote
 *
 * @package Aheadworks\Ctq\Controller\Quote\External
 */
class ReQuote extends ExternalAction
{
    /**
     * @var BuyerQuoteManagementInterface
     */
    private $buyerQuoteManagement;

    /**
     * @param Context $context
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param ItemsChecker $itemsChecker
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        Context $context,
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        ItemsChecker $itemsChecker,
        QuoteRepositoryInterface $quoteRepository
    ) {
        parent::__construct(
            $context,
            $itemsChecker,
            $quoteRepository
        );
        $this->buyerQuoteManagement = $buyerQuoteManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $quote = $this->buyerQuoteManagement->copyQuote($this->getQuoteByHash());
            return $resultRedirect->setPath(
                '*/*/external_edit',
                ['hash' => $quote->getHash()]
            );
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while duplicating the quote.')
            );
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
