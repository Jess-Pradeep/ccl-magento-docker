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

namespace Aheadworks\Ca\Controller\Adminhtml\Company;

use Aheadworks\Ca\Model\Data\CommandInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction as CustomerAbstractMassAction;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Aheadworks\Ca\Api\Data\CompanyUserInterface;

/**
 * Class MassUnassignCustomer
 */
class MassUnassignCustomer extends CustomerAbstractMassAction
{
    /**
     * @var string
     */
    protected $redirectUrl = 'customer/index';

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

    /**
     * @var CommandInterface
     */
    private $massActionCommand;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CommandInterface $massActionCommand
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CommandInterface $massActionCommand
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->massActionCommand = $massActionCommand;
    }

    /**
     * Perform mass action
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    protected function massAction(AbstractCollection $collection): ?ResultInterface
    {
        $updatedRecords = 0;
        /** @var CustomerInterface $customer */
        foreach ($collection->getAllIds() as $customerId) {
            try {
                $isUnassigned = $this->massActionCommand->execute([CompanyUserInterface::CUSTOMER_ID => (int)$customerId]);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                continue;
            }
            if ($isUnassigned) {
                $updatedRecords++;
            }
        }

        if ($updatedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $updatedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records have been updated.'));
        }

        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('customer/index');
    }
}
