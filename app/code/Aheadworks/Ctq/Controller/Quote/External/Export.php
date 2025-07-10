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
use Aheadworks\Ctq\ViewModel\Customer\Quote\Locator;
use Magento\Framework\App\Action\Context;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\Source\Quote\Export\Type;
use Aheadworks\Ctq\Model\Quote\Export\Composite as QuoteExporter;
use Aheadworks\Ctq\Controller\ExternalAction;

/**
 * Class Export
 *
 * @package Aheadworks\Ctq\Controller\Quote\External
 */
class Export extends ExternalAction
{
    /**
     * @var QuoteExporter
     */
    private $quoteExporter;

    /**
     * @param Context $context
     * @param QuoteRepositoryInterface $quoteRepository
     * @param ItemsChecker $itemsChecker
     * @param QuoteExporter $quoteExporter
     */
    public function __construct(
        Context $context,
        QuoteRepositoryInterface $quoteRepository,
        ItemsChecker $itemsChecker,
        QuoteExporter $quoteExporter
    ) {
        parent::__construct($context, $itemsChecker, $quoteRepository);
        $this->quoteExporter = $quoteExporter;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            return $this->quoteExporter->exportQuote(
                $this->getQuoteByHash(),
                Type::DOC,
                Locator::LOCATE_BY_HASH
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
