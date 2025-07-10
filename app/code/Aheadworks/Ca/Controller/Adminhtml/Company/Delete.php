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
namespace Aheadworks\Ca\Controller\Adminhtml\Company;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Aheadworks\Ca\Api\CompanyManagementInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Ca\Controller\Adminhtml\Company
 */
class Delete extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var CompanyManagementInterface
     */
    private $companyService;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CompanyManagementInterface $companyService
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CompanyManagementInterface $companyService
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->companyService = $companyService;
    }

    /**
     * Delete company action
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $companyId = (int)$this->getRequest()->getParam('id');
        if ($companyId) {
            try {
                $this->companyService->removeCompany($companyId);
                $this->messageManager->addSuccessMessage(__('The company and its users were successfully deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Something went wrong while deleting the company.'));
        return $resultRedirect->setPath('*/*/');
    }
}
