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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Controller\Balance;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Magento\Customer\Model\Session;
use Magento\Checkout\Model\Session as CheckoutSession;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Model\Data\CommandInterface;
use Aheadworks\CreditLimit\Model\Product\BalanceUnit\Provider as BalanceUnitProvider;

/**
 * Class MakePayment
 * Handles the process of making a payment toward a credit limit balance.
 *
 */
class MakePayment extends AbstractAction
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var BalanceUnitProvider
     */
    private $balanceUnitProvider;

    /**
     * @var CommandInterface
     */
    private $makePaymentCommand;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerManagementInterface $customerManagement
     * @param BalanceUnitProvider $balanceUnitProvider
     * @param CheckoutSession $checkoutSession
     * @param CommandInterface $makePaymentCommand
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerManagementInterface $customerManagement,
        BalanceUnitProvider $balanceUnitProvider,
        CheckoutSession $checkoutSession,
        CommandInterface $makePaymentCommand
    ) {
        parent::__construct($context, $customerSession, $customerManagement);
        $this->checkoutSession = $checkoutSession;
        $this->balanceUnitProvider = $balanceUnitProvider;
        $this->makePaymentCommand = $makePaymentCommand;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $quote = $this->checkoutSession->getQuote();
        $customerId = $this->getRequest()->getParam('customer_id') ?? $this->customerSession->getCustomerId();

        if ($quote->getCustomerId() != $customerId) {
            throw new LocalizedException(__('Current customer is not allowed.'));
        }

        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->makePaymentCommand->execute(
                [
                    'quote' => $quote,
                    'amount' => $this->getRequest()->getParam('amount')
                ]
            );
            $balanceUnit = $this->balanceUnitProvider->getProduct();
            $this->messageManager->addSuccessMessage(
                __('You added %1 to your shopping cart.', $balanceUnit->getName())
            );
            return $resultRedirect->setPath('checkout/cart');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('You cannot make a payment right now.')
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
