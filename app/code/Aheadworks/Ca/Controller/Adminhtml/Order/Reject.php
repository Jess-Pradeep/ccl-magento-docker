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
namespace Aheadworks\Ca\Controller\Adminhtml\Order;

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Aheadworks\Ca\Model\Role\OrderApproval\OrderManager;

/**
 * Class Reject
 *
 * @package Aheadworks\Ca\Controller\Adminhtml\Order
 */
class Reject extends Action
{
    const ADMIN_RESOURCE = 'Aheadworks_Ca::order_reject';

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderManager
     */
    private $orderManager;

    /**
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderManager $orderManager
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        OrderManager $orderManager
    ) {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
        $this->orderManager = $orderManager;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($orderId) {
            try {
                $order = $this->orderRepository->get($orderId);
                $this->orderManager->rejectOrder($order);
                $this->messageManager->addSuccessMessage(__('The order has been rejected'));
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while rejecting the order')
                );
            }

            return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        }

        return $resultRedirect->setPath('sales/*/');
    }
}
