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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\QuoteList\State;
use Aheadworks\Ctq\Model\Request\Checker;
use Aheadworks\Ctq\Model\Data\CommandInterface;

/**
 * Class Submit
 *
 * @package Aheadworks\Ctq\Controller\RequestQuote
 */
class Submit extends Action
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var State
     */
    private $state;

    /**
     * @var Checker
     */
    private $checker;

    /**
     * @var CommandInterface
     */
    private $submitCommand;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param Checker $checker
     * @param State $state
     * @param CommandInterface $submitCommand
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        Checker $checker,
        State $state,
        CommandInterface $submitCommand
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->state = $state;
        $this->checker = $checker;
        $this->submitCommand = $submitCommand;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            if ($this->checker->isQuoteList()) {
                $quote = $this->state->emulateQuote([$this, 'requestQuote'], [true]);
            } else {
                $quote = $this->requestQuote();
            }

            $this->checkoutSession
                ->setAwCtqLastQuoteId($quote->getId())
                ->setAwCtqLastRealQuoteId($quote->getId());
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong when requesting a quote.')
            );
        }

        return $resultRedirect->setPath('*/requestQuote/success');
    }

    /**
     * Request quote
     *
     * @param bool $isQuoteList
     * @return QuoteInterface
     * @throws LocalizedException
     */
    public function requestQuote($isQuoteList = false)
    {
        $data = [
            'is_guest_quote' => !$this->customerSession->isLoggedIn(),
            'quote_id' => $this->checkoutSession->getQuoteId(),
            'request' => $this->getRequest(),
            'is_quote_list' => $isQuoteList
        ];

        return $this->submitCommand->execute($data);
    }
}
