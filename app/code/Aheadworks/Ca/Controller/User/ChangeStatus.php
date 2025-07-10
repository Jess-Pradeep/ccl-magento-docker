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
declare(strict_types=1);

namespace Aheadworks\Ca\Controller\User;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ca\Model\Data\Command\User\UserCommandPool;

class ChangeStatus extends AbstractUserAction
{
    /**
     * Check if entity belongs to customer
     */
    public const IS_ENTITY_BELONGS_TO_CUSTOMER = true;

    /**
     * ChangeStatus Construct
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param UserCommandPool $userCommandPool
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        private readonly UserCommandPool $userCommandPool
    ) {
        parent::__construct($context, $customerSession, $customerRepository);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $customerId = $this->getEntityIdByRequest();

        if ($customerId) {
            try {
                if ($this->getCurrentCompanyUser()->getId() == $customerId) {
                    throw new LocalizedException(__('The user status can\'t be changed.'));
                }

                $data = [
                    'customer_id' => $customerId,
                    'status' => $this->getRequest()->getParam('activate')
                ];
                $this->userCommandPool->executeCommands($data);
                $this->messageManager->addSuccessMessage(
                    __('The user status has been changed.')
                );
            } catch (NoSuchEntityException $exception) {
                throw new NotFoundException(__('Page not found.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
