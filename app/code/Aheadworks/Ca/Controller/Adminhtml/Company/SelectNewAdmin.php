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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;

class SelectNewAdmin extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

    /**
     * @param Context $context
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        Context $context,
        private readonly CompanyUserManagementInterface $companyUserManagement
    ) {
        parent::__construct($context);
    }

    /**
     * Select new company admin action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $companyId = $this->getRequest()->getParam('company_id');
        $newCompanyAdminId = $this->getRequest()->getParam('new_company_admin_id');

        if ($companyId && $newCompanyAdminId) {
            try {
                $this->companyUserManagement->assignNewAdminToCompany((int)$newCompanyAdminId, (int)$companyId);
                $this->messageManager->addSuccessMessage(
                    __('New Company Administrator has been successfully selected.')
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while selecting new Company Administrator.')
                );
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $companyId, '_current' => true]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
