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
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Exception\LocalizedException;

class ReQuote extends Save
{
    /**
     * Requote action
     *
     * @return ResultRedirect
     */
    public function execute(): ResultRedirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $postData = $this->getRequest()->getPostValue();
        if ($postData) {
            try {
                $data = $this->postDataProcessor->preparePostData($postData);
                $quote = $this->sellerQuoteManagement->updateQuote($this->prepareQuote($data));
                $newQuote = $this->sellerQuoteManagement->copyQuote($quote);
                $this->messageManager->addSuccessMessage(__('Quote was duplicated successfully'));
                return $this->redirectTo($resultRedirect, $newQuote);
            } catch (LocalizedException|\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while duplicating the quote'));
            }
            $quoteId = $data['quote']['quote_id'] ?? null;
            if ($quoteId) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $quoteId, '_current' => true]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
