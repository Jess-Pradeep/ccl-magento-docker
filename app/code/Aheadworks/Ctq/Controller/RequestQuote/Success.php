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
namespace Aheadworks\Ctq\Controller\RequestQuote;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;

/**
 * Class Success
 *
 * @package Aheadworks\Ctq\Controller\RequestQuote
 */
class Success extends Action
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (!$this->isValid()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        $resultPage = $this->resultPageFactory->create();
        $this->checkoutSession
            ->setAwCtqLastQuoteId(null)
            ->setAwCtqQuoteListId(null);

        return $resultPage;
    }

    /**
     * Check if is valid
     *
     * @return bool
     */
    private function isValid()
    {
        return $this->checkoutSession->getAwCtqLastQuoteId() && $this->checkoutSession->getAwCtqLastRealQuoteId();
    }
}
