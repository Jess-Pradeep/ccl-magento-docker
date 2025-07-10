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
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Controller\ExternalAction;
use Aheadworks\Ctq\Model\Data\CommandInterface;

/**
 * Class Download
 *
 * @package Aheadworks\Ctq\Controller\Quote\External
 */
class Download extends ExternalAction
{
    /**
     * @var CommandInterface
     */
    private $downloadCommand;

    /**
     * @param Context $context
     * @param QuoteRepositoryInterface $quoteRepository
     * @param ItemsChecker $itemsChecker
     * @param CommandInterface $downloadCommand
     */
    public function __construct(
        Context $context,
        QuoteRepositoryInterface $quoteRepository,
        ItemsChecker $itemsChecker,
        CommandInterface $downloadCommand
    ) {
        parent::__construct($context, $itemsChecker, $quoteRepository);
        $this->downloadCommand = $downloadCommand;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $quote = $this->getQuoteByHash();
            $data = [
                'file' => $this->getRequest()->getParam('file'),
                'comment_id' => $this->getRequest()->getParam('comment_id'),
                'quote_id' => $quote->getId()
            ];
            return $this->downloadCommand->execute($data);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
