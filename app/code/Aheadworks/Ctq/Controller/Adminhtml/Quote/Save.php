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
declare(strict_types=1);

namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Exception;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action as BackendAction;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\Admin\UpdateProcessor;
use Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\PostDataProcessor;
use Aheadworks\Ctq\Api\SellerQuoteManagementInterface;
use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;
use Aheadworks\Ctq\Controller\Adminhtml\Quote\Edit\QuoteProcessor;
use Aheadworks\Ctq\Model\Data\CommandInterface;

class Save extends BackendAction
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Aheadworks_Ctq::quotes';

    /**
     * @param Context $context
     * @param UpdateProcessor $updateProcessor
     * @param PostDataProcessor $postDataProcessor
     * @param SellerQuoteManagementInterface $sellerQuoteManagement
     * @param QuoteSession $quoteSession
     * @param QuoteProcessor $quoteProcessor
     * @param CommandInterface $saveBackendQuote
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        protected readonly UpdateProcessor $updateProcessor,
        protected readonly PostDataProcessor $postDataProcessor,
        protected readonly SellerQuoteManagementInterface $sellerQuoteManagement,
        protected readonly QuoteSession $quoteSession,
        protected readonly QuoteProcessor $quoteProcessor,
        protected readonly CommandInterface $saveBackendQuote,
        protected readonly DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return ResultRedirect
     */
    public function execute(): ResultRedirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $postData = $this->getRequest()->getPostValue();
        if ($postData) {
            try {
                $this->updateProcessor->processRequest($this->getRequest());
                $data = $this->postDataProcessor->preparePostData($postData);
                $this->updateProcessor->updateData('save');

                $quote = $this->saveBackendQuote->execute(
                    [
                        'session_cart_id' => $this->quoteSession->getQuoteId(),
                        'cart' => $this->updateProcessor->getQuote(),
                        'quote' => $this->prepareQuote($data)
                    ]
                );

                $this->dataPersistor->clear(UpdateProcessor::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('Quote was saved successfully'));
                return $this->redirectTo($resultRedirect, $quote);
            } catch (LocalizedException|\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the quote'));
            }
            $this->dataPersistor->set(UpdateProcessor::DATA_PERSISTOR_FORM_DATA_KEY, $data['quote'] ?? []);
            $quoteId = $data['quote']['quote_id'] ?? null;
            if ($quoteId) {
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['id' => $quoteId, '_current' => true, 'saveOnReload' => '1']
                );
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true, 'saveOnReload' => '1']);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Prepare quote
     *
     * @param array $data
     * @return QuoteInterface
     * @throws Exception
     */
    protected function prepareQuote(array $data): QuoteInterface
    {
        return $this->quoteProcessor->prepare($data);
    }

    /**
     * Redirect to
     *
     * @param Redirect $resultRedirect
     * @param QuoteInterface $quote
     * @return Redirect
     */
    protected function redirectTo(Redirect $resultRedirect, QuoteInterface $quote): Redirect
    {
        return $resultRedirect->setPath('*/*/edit', ['id' => $quote->getId()]);
    }
}
