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
namespace Aheadworks\Ca\Controller\Role;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ca\Ui\DataProvider\FormDataProvider;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Model\Data\Command\Role\Save as RoleSaveCommand;
use Aheadworks\Ca\Api\Data\RoleInterface;

/**
 * Class SavePost
 *
 * @package Aheadworks\Ca\Controller\Role
 */
class SavePost extends AbstractRoleAction
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @var CommandInterface
     */
    private $saveCommand;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param RoleRepositoryInterface $roleRepository
     * @param DataPersistorInterface $dataPersistor
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param CommandInterface $saveCommand
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        RoleRepositoryInterface $roleRepository,
        DataPersistorInterface $dataPersistor,
        AuthorizationManagementInterface $authorizationManagement,
        CommandInterface $saveCommand
    ) {
        parent::__construct($context, $customerSession, $roleRepository);
        $this->dataPersistor = $dataPersistor;
        $this->authorizationManagement = $authorizationManagement;
        $this->saveCommand = $saveCommand;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $roleId = $this->getRequest()->getParam(RoleInterface::ID);
                if (($roleId
                        && !$this->authorizationManagement->isAllowedByResource('Aheadworks_Ca::company_roles_edit'))
                    || (!$roleId
                        && !$this->authorizationManagement->isAllowedByResource('Aheadworks_Ca::company_roles_add_new'))
                ) {
                    return $resultRedirect->setUrl($this->_url->getUrl('noroute'));
                }

                $data[RoleSaveCommand::CURRENT_COMPANY_ID] = $this->getCurrentCompanyId();
                $this->saveCommand->execute($data);
                $this->dataPersistor->clear(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('The Role was saved successfully.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while save the role.')
                );
            }
            $this->dataPersistor->set(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
            $roleId = $data[RoleInterface::ID] ?? false;
            if ($roleId) {
                return $resultRedirect->setPath('*/*/edit', [RoleInterface::ID => $roleId, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/create', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
