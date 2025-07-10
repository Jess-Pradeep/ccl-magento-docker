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

namespace Aheadworks\Ca\Controller\Unit;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ca\Api\UnitRepositoryInterface;
use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class Delete extends AbstractUnitAction
{
    /**
     * Delete Constructor
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param UnitRepositoryInterface $unitRepository
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param JsonFactory $jsonResultFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        private readonly UnitRepositoryInterface $unitRepository,
        private readonly AuthorizationManagementInterface $authorizationManagement,
        private readonly JsonFactory $jsonResultFactory
    ) {
        parent::__construct($context, $customerSession, $unitRepository, $jsonResultFactory);
    }

    /**
     * Delete unit.
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Json $result */
        $resultJson = $this->jsonResultFactory->create();
        $unitId = $this->getRequest()->getParam('unitId', 0);
        $result = ['success' => false];
        if ($unitId) {
            try {
                $companyId = $this->getCurrentCompanyId();
                $rootUnit = $this->unitRepository->getCompanyRootUnit($companyId);
                if ($rootUnit->getId() == $unitId) {
                    $result['message'] = __('The Root Unit cannot be deleted.');
                    return $resultJson->setData($result);
                }
                $this->unitRepository->deleteById($unitId);
                $this->messageManager->addSuccessMessage(__('Unit was successfully deleted.'));
                $result['success'] = true;
            } catch (\Exception $exception) {
                $result['message'] = $exception->getMessage();
            }
        } else {
            $result['message'] = __('No unit data provided.');
        }

        return $resultJson->setData($result);
    }
}
