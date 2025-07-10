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
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Aheadworks\Ctq\Controller\ExternalAction;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Data\CommandInterface;

/**
 * Class ChangeStatus
 *
 * @package Aheadworks\Ctq\Controller\Quote
 */
class ChangeStatus extends ExternalAction
{
    /**
     * @var CommandInterface
     */
    private $changeStatusCommand;

    /**
     * @param Context $context
     * @param QuoteRepositoryInterface $quoteRepository
     * @param ItemsChecker $itemsChecker
     * @param CommandInterface $changeStatusCommand
     */
    public function __construct(
        Context $context,
        QuoteRepositoryInterface $quoteRepository,
        ItemsChecker $itemsChecker,
        CommandInterface $changeStatusCommand
    ) {
        parent::__construct(
            $context,
            $itemsChecker,
            $quoteRepository
        );
        $this->changeStatusCommand = $changeStatusCommand;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $quote = $this->getQuoteByHash();
            if (!$this->isQuoteCanBeEdited($quote)) {
                return $resultRedirect->setPath('*/*/');
            }
            $this->changeStatus(Status::PENDING_SELLER_REVIEW);
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
     * Change status
     *
     * @param string $status
     * @return void
     * @throws LocalizedException
     */
    protected function changeStatus($status)
    {
        $data = [
            'status' => $status,
            'quote_id' => $this->getQuoteByHash()->getId()
        ];
        $this->changeStatusCommand->execute($data);
    }
}
