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

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Aheadworks\Ca\Ui\DataProvider\FormDataProvider;
use Aheadworks\Ca\Controller\Company\DataProcessor;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\Company\Updater;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param DataProcessor $dataProcessor
     * @param Updater $creditLimitUpdater
     * @param CommandInterface $processSavingCommand
     */
    public function __construct(
        Context $context,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly DataProcessor $dataProcessor,
        private readonly Updater $creditLimitUpdater,
        private readonly CommandInterface $processSavingCommand
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $requestData = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($requestData) {
            $companyId = isset($requestData['company']['id']) ? (int)$requestData['company']['id'] : null;
            try {
                $customer = $this->dataProcessor->prepareCustomer($this->getRequest());
                $company = $this->dataProcessor->prepareCompany($this->getRequest());

                $this->processSavingCommand->execute([
                    'customer' => $customer,
                    'company' => $company
                ]);

                $this->postCompanySave($company);

                $companyId = $company->getId();
                $this->dataPersistor->clear(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('The company was successfully saved.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $companyId, '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while saving the company.')
                );
            }

            $this->dataPersistor->set(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $requestData);
            if ($companyId) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $companyId, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Post company save
     *
     * @param CompanyInterface $company
     * @throws LocalizedException
     */
    public function postCompanySave(CompanyInterface $company)
    {
        $this->creditLimitUpdater->updateCreditLimit(
            $company->getId(),
            $this->getRequest()->getParam(Updater::CREDIT_LIMIT_PARAM)
        );
    }
}
