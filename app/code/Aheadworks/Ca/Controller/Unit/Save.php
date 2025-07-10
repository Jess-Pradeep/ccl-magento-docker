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
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ca\Ui\DataProvider\FormDataProvider;
use Aheadworks\Ca\Api\UnitRepositoryInterface;
use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\Data\UnitInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Model\Data\Command\Company\Unit\Save as UnitSaveCommand;

class Save extends AbstractUnitAction
{
    /**
     * Save Constructor
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param UnitRepositoryInterface $unitRepository
     * @param DataPersistorInterface $dataPersistor
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param CommandInterface $saveCommand
     * @param JsonFactory $jsonResultFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        private readonly UnitRepositoryInterface $unitRepository,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly AuthorizationManagementInterface $authorizationManagement,
        private readonly CommandInterface $saveCommand,
        JsonFactory $jsonResultFactory
    ) {
        parent::__construct($context, $customerSession, $unitRepository, $jsonResultFactory);
    }

    /**
     * Save action
     *
     * @return ResultRedirect
     */
    public function execute(): ResultRedirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($unitData = $this->getRequest()->getPostValue()) {
            try {
                $unitId = $unitId = !empty($unitData['id']) ? $unitData['id'] : false;
                if (($unitId
                        && !$this->authorizationManagement
                            ->isAllowedByResource('Aheadworks_Ca::company_units_edit'))
                    || (!$unitId
                        && !$this->authorizationManagement
                            ->isAllowedByResource('Aheadworks_Ca::company_units_add_new'))
                ) {
                    return $resultRedirect->setUrl($this->_url->getUrl('noroute'));
                }
                $unitData['id'] = $unitId;
                $unitData[UnitSaveCommand::CURRENT_COMPANY_ID] = $this->getCurrentCompanyId();
                $this->saveCommand->execute($unitData);
                $this->dataPersistor->clear(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('The Unit was saved successfully.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while save the unit.')
                );
            }
            $this->dataPersistor->set(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $unitData);
            $unitId = $data[UnitInterface::UNIT_ID] ?? false;
            if ($unitId) {
                return $resultRedirect->setPath(
                    '*/*/edit',
                    [UnitInterface::UNIT_ID => $unitId, '_current' => true]
                );
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
