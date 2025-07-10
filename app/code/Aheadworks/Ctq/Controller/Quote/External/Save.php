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
use Aheadworks\Ctq\Model\Exception\UpdateForbiddenException;
use Magento\Framework\App\Action\Context;
use Aheadworks\Ctq\Controller\ExternalAction;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Data\CommandInterface;

/**
 * Class Save
 *
 * @package Aheadworks\Ctq\Controller\Quote\External
 */
class Save extends ExternalAction
{
    /**
     * @var CommandInterface
     */
    private $saveCommand;

    /**
     * @param Context $context
     * @param QuoteRepositoryInterface $quoteRepository
     * @param ItemsChecker $itemsChecker
     * @param CommandInterface $saveCommand
     */
    public function __construct(
        Context $context,
        QuoteRepositoryInterface $quoteRepository,
        ItemsChecker $itemsChecker,
        CommandInterface $saveCommand
    ) {
        parent::__construct($context, $itemsChecker, $quoteRepository);
        $this->saveCommand = $saveCommand;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $quote = $this->getQuoteByHash();
        try {
            if (!$this->isQuoteCanBeEdited($quote)) {
                return $resultRedirect->setPath('*/*/');
            }
            $data = [
                'quote' => $quote,
                'cart' => $this->getRequest()->getParam('cart')
            ];

            $this->saveCommand->execute($data);
            return $this->redirectTo($resultRedirect);
        } catch (UpdateForbiddenException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $data = [
                'quote' => $quote,
                'cart' => false
            ];
            $this->saveCommand->execute($data);
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while save the quote.')
            );
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
