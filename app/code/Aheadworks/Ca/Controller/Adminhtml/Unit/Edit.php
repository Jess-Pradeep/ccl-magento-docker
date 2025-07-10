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

use Aheadworks\Ca\Api\UnitRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

    /**
     * Edit Construct
     *
     * @param Context $context
     * @param UnitRepositoryInterface $unitRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        private readonly UnitRepositoryInterface $unitRepository,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    /**
     * Edit action
     *
     * @return ResultPage|ResultRedirect
     */
    public function execute()
    {
        $unitId = (int)$this->getRequest()->getParam('id');
        $companyId = (int)$this->getRequest()->getParam('company_id');

        if (!$companyId) {
            $this->messageManager->addErrorMessage(
                __('This company does not exist.')
            );
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('aw_ca/company/index/');
            return $resultRedirect;
        }

        if ($unitId) {
            try {
                $unit = $this->unitRepository->get($unitId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This unit does not exist.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('aw_ca/company/edit/', ['id' => $companyId]);
                return $resultRedirect;
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_Ca::companies')
            ->getConfig()->getTitle()->prepend(
                $unitId
                    ? __('Edit "%1" unit', $unit->getUnitTitle())
                    : __('Unit Information')
            );
        return $resultPage;
    }
}
