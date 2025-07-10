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

namespace Aheadworks\Ca\Controller\Adminhtml\Company\Admin;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Api\CompanyAdminCandidateManagementInterface;
use Aheadworks\Ca\Api\CompanyAdminCandidateRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ApproveCandidate extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

    /**
     * @param Context $context
     * @param CompanyAdminCandidateManagementInterface $candidateManagement
     * @param CompanyAdminCandidateRepositoryInterface $candidateRepository
     */
    public function __construct(
        Context $context,
        private readonly CompanyAdminCandidateManagementInterface $candidateManagement,
        private readonly CompanyAdminCandidateRepositoryInterface $candidateRepository
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @throws NoSuchEntityException
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $candidateId = $this->getRequest()->getParam('candidate_id');

        if ($candidateId) {
            try {
                $this->candidateManagement->approve((int)$candidateId);
                $this->messageManager->addSuccessMessage(
                    __('New Company Administrator change request has been approved.')
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while approving new Company Administrator request.')
                );
            }

            $candidate = $this->candidateRepository->get((int)$candidateId);
            return $resultRedirect->setPath('*/company/edit', ['id' => $candidate->getCompanyId(), '_current' => true]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
