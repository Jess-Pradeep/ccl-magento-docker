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

namespace Aheadworks\Ca\Controller\User\Import;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Controller\User\AbstractUserAction;
use Aheadworks\Ca\Model\FileSystem\File\Uploader as FileUploader;
use Aheadworks\Ca\Model\Import\User\Validator as UserValidator;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Aheadworks\Ca\Model\Import\User\Import as UserImport;

class Start extends AbstractUserAction implements HttpPostActionInterface
{
    private const INPUT_FILE_NAME = 'csv-file-to-import';

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param FileUploader $fileUploader
     * @param UserValidator $userValidator
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param UserImport $userImport
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        private readonly FileUploader $fileUploader,
        private readonly UserValidator $userValidator,
        private readonly AuthorizationManagementInterface $authorizationManagement,
        private readonly UserImport $userImport
    ) {
        parent::__construct($context, $customerSession, $customerRepository);
    }

    /**
     * Start user file import
     *
     * @return ResultInterface
     * @throws NoSuchEntityException|FileSystemException
     * @throws LocalizedException
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $companyUser = $this->getCurrentCompanyUser();
        if (!$companyUser
            || !$this->authorizationManagement->isAllowedByResource('Aheadworks_Ca::company_users_add_new')
        ) {
            return $resultRedirect->setUrl($this->_url->getUrl('noroute'));
        }

        $csvFile = $this->fileUploader->upload(self::INPUT_FILE_NAME);
        $csvSource = $this->fileUploader->getSource($csvFile);

        $messages = $this->userValidator->validateSource($csvSource);
        if ($messages) {
            $this->messageManager->addComplexErrorMessage(
                'awCaImportErrorMessage',
                [
                    'title' => __(
                        'Data validation failed. Please fix the following errors and upload the file again:'
                    )->render(),
                    'messages' => $this->prepareMessages($messages)
                ]
            );
            return $resultRedirect->setPath('*/user/import');
        }

        $messages = $this->userImport->process($csvSource);
        if ($messages) {
            $this->messageManager->addComplexErrorMessage(
                'awCaImportErrorMessage',
                [
                    'title' => __(
                        'Some records are not imported. The following errors appear while data importing:'
                    )->render(),
                    'messages' => $this->prepareMessages($messages)
                ]
            );
            return $resultRedirect->setPath('*/user/import');
        }

        $this->messageManager->addSuccessMessage(__('Import has been completed'));
        return $resultRedirect->setPath('*/user/index');
    }

    /**
     * Prepare messages
     *
     * @param array $messages
     * @return array
     */
    private function prepareMessages(array $messages): array
    {
        return array_map(
            fn ($message): string => $message instanceof Phrase
                ? $message->render()
                : $message,
            $messages
        );
    }
}
