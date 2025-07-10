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

use Aheadworks\Ctq\Model\Quote\Cart\ItemsChecker;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Aheadworks\Ctq\Api\Data\QuoteInterface;

/**
 * Class AbstractAction
 *
 * @package Aheadworks\Ctq\Controller
 */
abstract class AbstractAction extends Action
{
    /**
     * @var ItemsChecker
     */
    private $itemsChecker;

    /**
     * @param Context $context
     * @param ItemsChecker $itemsChecker
     */
    public function __construct(
        Context $context,
        ItemsChecker $itemsChecker
    ) {
        parent::__construct($context);
        $this->itemsChecker = $itemsChecker;
    }

    /**
     * Check that quote edited at the same store as requested
     *
     * @param QuoteInterface $quote
     * @return bool
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function isQuoteCanBeEdited($quote)
    {
        $result = true;
        $failItems = $this->itemsChecker->checkData($quote);

        if ($failItems) {
            $this->messageManager->addErrorMessage(
                __(
                    "Sorry, %1 is not available for quoting. Please contact the merchant.",
                    implode(', ', $failItems)
                )
            );

            $result = false;
        }

        return $result;
    }

    /**
     * Redirect to
     *
     * @param Redirect $resultRedirect
     * @return Redirect
     */
    protected function redirectTo($resultRedirect)
    {
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
