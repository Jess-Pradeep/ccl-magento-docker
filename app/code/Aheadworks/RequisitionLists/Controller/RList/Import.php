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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Model\Import\FileContentProcessor;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Aheadworks\RequisitionLists\Model\Import\ProcessingErrorAggregator;
use Aheadworks\RequisitionLists\Model\RequisitionList\Provider;
use Aheadworks\RequisitionLists\Model\RequisitionList\Manager;
use Aheadworks\UiComponents\Controller\Import\Upload;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Phrase;
use Magento\Framework\View\Result\PageFactory;

class Import extends AbstractRequisitionListAction
{
    /**
     * @param Provider $provider
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ResponseFactory $responseFactory
     * @param RequisitionListRepositoryInterface $requisitionListRepository
     * @param PageFactory $pageFactory
     * @param FileContentProcessor $fileContentProcessor
     * @param Manager $requisitionListManager
     * @param ProcessingErrorAggregator $errorAggregator
     */
    public function __construct(
        Provider $provider,
        Context $context,
        CustomerSession $customerSession,
        ResponseFactory $responseFactory,
        RequisitionListRepositoryInterface $requisitionListRepository,
        PageFactory $pageFactory,
        private readonly FileContentProcessor $fileContentProcessor,
        private readonly Manager $requisitionListManager,
        private readonly ProcessingErrorAggregator $errorAggregator
    ) {
        parent::__construct(
            $provider,
            $context,
            $customerSession,
            $responseFactory,
            $requisitionListRepository,
            $pageFactory
        );
    }

    /**
     * Import action
     *
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result = [];
        try {
            if ($data = $this->getRequest()->getPostValue()) {
                $result = $this->performSave($data);
            }
        } catch (\Exception $e) {
            $result = [
                'messages' => $e->getMessage(),
                'error' => true
            ];
        }

        return $resultJson->setData($result);
    }

    /**
     * Perform save
     *
     * @param array $data
     * @return string[]
     */
    private function performSave(array $data): array
    {
        $result = [
            'messages' => __('File is not uploaded')
        ];
        if ($fullPathToFile = $this->getFullPathToFile($data)) {
            $itemsForImport = $this->fileContentProcessor->process($fullPathToFile);
            $importedRecords = $this->requisitionListManager->importItemsFromData(
                $itemsForImport,
                (int)($data['list_id'] ?? 0)
            );

            $result['messages'] = $this->getResultMessages(count($importedRecords));
        }

        return $result;
    }

    /**
     * Retrieves full path to already uploaded file with data for import
     *
     * @param array $data
     * @return string|null
     */
    private function getFullPathToFile(array $data): ?string
    {
        return !empty($data[Upload::FILE_ID][0]['full_path']) ? $data[Upload::FILE_ID][0]['full_path'] : null;
    }

    /**
     * Retrieves result messages
     *
     * @param int $importedRecordsCount
     * @return array
     */
    private function getResultMessages(int $importedRecordsCount): array
    {
        $messages = [
            __(
                'Import successfully completed! %1 products have been imported.',
                $importedRecordsCount
            )
        ];

        $errorMessage = $this->errorAggregator->generateErrorMessageWithContainer();

        if ($errorMessage) {
            $messages[] = $errorMessage;
        }

        return $messages;
    }
}
