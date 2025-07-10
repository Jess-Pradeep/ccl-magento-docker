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

namespace Aheadworks\Ca\Controller\Adminhtml\Unit;

use Magento\Backend\App\Action\Context;
use Aheadworks\Ca\Api\UnitRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;

class Delete extends Action
{
    /**
     * Delete construct
     *
     * @param Context $context
     * @param UnitRepositoryInterface $unitRepository
     */
    public function __construct(
        Context $context,
        private readonly UnitRepositoryInterface $unitRepository
    ) {
        parent::__construct($context);
    }

    /**
     * Delete the unit
     *
     * @return ResultRedirect
     */
    public function execute(): ResultRedirect
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $unitId = $this->getRequest()->getParam('id', 0);
        $companyId = $this->getRequest()->getParam('company_id', 0);
        if ($unitId) {
            try {
                $this->unitRepository->deleteById($unitId);
                $this->messageManager->addSuccessMessage(__('Unit was successfully deleted.'));
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong.')
                );
                $resultRedirect->setPath('aw_ca/unit/edit/', ['company_id' => $companyId, 'id' => $unitId]);
                return $resultRedirect;
            }
        } else {
            $this->messageManager->addErrorMessage(__('No unit data provided.'));
        }

        $resultRedirect->setPath('aw_ca/company/edit/', ['id' => $companyId]);
        return $resultRedirect;
    }
}
