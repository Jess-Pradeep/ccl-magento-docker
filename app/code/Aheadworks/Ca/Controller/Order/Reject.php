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
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Controller\Order;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Reject
 *
 * @package Aheadworks\Ca\Controller\Order
 */
class Reject extends AbstractOrderAction
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->orderApprovalManagement->rejectOrder($this->getEntity());
            $this->messageManager->addSuccessMessage(__('The order has been rejected'));
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while approving the order')
            );
        }

        return $resultRedirect->setPath('sales/order/history');
    }
}
