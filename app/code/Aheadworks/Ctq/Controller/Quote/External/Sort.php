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
 * Class Sort
 *
 * @package Aheadworks\Ctq\Controller\Quote\External
 */
class Sort extends ExternalAction
{
    /**
     * @var CommandInterface
     */
    private $sortCommand;

    /**
     * @param Context $context
     * @param QuoteRepositoryInterface $quoteRepository
     * @param ItemsChecker $itemsChecker
     * @param CommandInterface $sortCommand
     */
    public function __construct(
        Context $context,
        QuoteRepositoryInterface $quoteRepository,
        ItemsChecker $itemsChecker,
        CommandInterface $sortCommand
    ) {
        parent::__construct($context, $itemsChecker, $quoteRepository);
        $this->sortCommand = $sortCommand;
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
                'sort' => (array)$this->getRequest()->getParam('sort'),
                'quote_id' => $quote->getId()
            ];
            $this->sortCommand->execute($data);
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
