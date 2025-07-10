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
namespace Aheadworks\Ctq\Block\Customer\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\CollectionFactory as MessageCollectionFactory;
use Magento\Framework\Message\Factory as MessageFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Validator\MinimumOrderAmount\ValidationMessage;
use Aheadworks\Ctq\ViewModel\Customer\Quote\DataProvider as QuoteDataProvider;
use Aheadworks\Ctq\Model\Source\Quote\Status;

/**
 * Class ValidationMessages
 *
 * @method string getMethodToLocateQuote()
 * @package Aheadworks\Ctq\Block\Customer\Quote
 */
class ValidationMessages extends Messages
{
    /**
     * @var ValidationMessage
     */
    private $minimumAmountErrorMessage;

    /**
     * @var QuoteDataProvider
     */
    private $dataProviderViewModel;

    /**
     * @param Context $context
     * @param MessageFactory $messageFactory
     * @param MessageCollectionFactory $collectionFactory
     * @param ManagerInterface $messageManager
     * @param InterpretationStrategyInterface $interpretationStrategy
     * @param ValidationMessage $validationMessage
     * @param QuoteDataProvider $quoteDataProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        MessageFactory $messageFactory,
        MessageCollectionFactory $collectionFactory,
        ManagerInterface $messageManager,
        InterpretationStrategyInterface $interpretationStrategy,
        ValidationMessage $validationMessage,
        QuoteDataProvider $quoteDataProvider,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $messageFactory,
            $collectionFactory,
            $messageManager,
            $interpretationStrategy,
            $data
        );
        $this->minimumAmountErrorMessage = $validationMessage;
        $this->dataProviderViewModel = $quoteDataProvider;
        $this->dataProviderViewModel->setMethodToLocateQuote($this->getMethodToLocateQuote());
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Currency_Exception
     */
    protected function _prepareLayout()
    {
        $quote = $this->dataProviderViewModel->getQuote();
        if ($quote->getStatus() == Status::PENDING_BUYER_REVIEW) {
            $cart = $this->dataProviderViewModel->getCart();
            $this->validateMinimumAmount($cart);
            $this->addQuoteMessages($cart);
            $this->addMessages($this->messageManager->getMessages(true));
        }

        return parent::_prepareLayout();
    }

    /**
     * Validate minimum amount and display notice in error
     *
     * @param CartInterface|Quote $cart
     * @throws \Zend_Currency_Exception
     */
    private function validateMinimumAmount($cart)
    {
        if (!$cart->validateMinimumAmount()) {
            $this->messageManager->addNoticeMessage($this->minimumAmountErrorMessage->getMessage());
        }
    }

    /**
     * Add quote messages
     *
     * @param CartInterface|Quote $cart
     */
    private function addQuoteMessages($cart)
    {
        $messages = [];
        /** @var MessageInterface $message */
        foreach ($cart->getMessages() as $message) {
            if ($message) {
                $message->setText($this->escapeHtml($message->getText()));
                $messages[] = $message;
            }
        }

        if ($messages) {
            $this->messageManager->addUniqueMessages($messages);
        }
    }
}
