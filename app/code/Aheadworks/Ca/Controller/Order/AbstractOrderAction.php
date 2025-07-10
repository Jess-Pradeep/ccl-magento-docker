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

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Aheadworks\Ca\Controller\AbstractCustomerAction;
use Aheadworks\Ca\Api\OrderApprovalManagementInterface;

/**
 * Class AbstractOrderAction
 *
 * @package Aheadworks\Ca\Controller\Order
 */
abstract class AbstractOrderAction extends AbstractCustomerAction
{
    /**
     * Check if entity belongs to customer
     */
    const IS_ENTITY_BELONGS_TO_CUSTOMER = false;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderApprovalManagementInterface
     */
    protected $orderApprovalManagement;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderApprovalManagementInterface $orderApprovalManagement
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        OrderRepositoryInterface $orderRepository,
        OrderApprovalManagementInterface $orderApprovalManagement
    ) {
        parent::__construct($context, $customerSession);
        $this->orderRepository = $orderRepository;
        $this->orderApprovalManagement = $orderApprovalManagement;
    }

    /**
     * @inheritdoc
     */
    protected function getEntityIdByRequest()
    {
        return (int)$this->getRequest()->getParam('order_id');
    }

    /**
     * Retrieve order
     *
     * @return OrderInterface
     * @throws NotFoundException
     */
    protected function getEntity()
    {
        try {
            $id = $this->getEntityIdByRequest();
            $entity = $this->orderRepository->get($id);
        } catch (NoSuchEntityException $e) {
            throw new NotFoundException(__('Page not found.'));
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    protected function isEntityBelongsToCustomer()
    {
        return true;
    }
}
