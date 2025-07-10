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

namespace Aheadworks\Ca\Controller\Company;

use Aheadworks\Ca\Api\CompanyRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Api\CompanyAdminCandidateManagementInterface;

class SelectNewAdmin extends AbstractCompanyAction
{
    /**
     * Check if entity belongs to customer
     */
    public const IS_ENTITY_BELONGS_TO_CUSTOMER = true;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CompanyRepositoryInterface $companyRepository
     * @param CompanyAdminCandidateManagementInterface $candidateManagement
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CompanyRepositoryInterface $companyRepository,
        private readonly CompanyAdminCandidateManagementInterface $candidateManagement
    ) {
        parent::__construct($context, $customerSession, $companyRepository);
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
                $this->candidateManagement->create((int)$newCompanyAdminId, (int)$companyId);
                $this->messageManager->addSuccessMessage(__('New Company Administrator has been sent for approval'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while selecting new Company Administrator.')
                );
            }
        }

        return $resultRedirect->setPath('aw_ca/company/index');
    }
}
