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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\Import\Processor;

use Aheadworks\CreditLimit\Model\Import\MessageManager;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Config;

/**
 * Class AbstractImport
 */
abstract class AbstractImport implements ImportProcessorInterface
{
    /**
     * AbstractProcessor constructor.
     *
     * @param Import $import
     * @param Config $importConfig
     * @param MessageManager $messageManager
     * @param array $configEntity
     */
    public function __construct(
        private Import $import,
        private Config $importConfig,
        private MessageManager $messageManager,
        private array $configEntity = []
    ) {
    }

    /**
     * Run import
     *
     * @param array $data
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function perform(array $data): bool
    {
        $result = false;

        $this->importConfig->merge($this->configEntity);
        $this->import->setData($data);

        $source = $this->import->uploadFileAndGetSource();
        $isValid = $this->import->validateSource($source);

        if ($isValid) {
            $result = $this->import->importSource();
        }

        $errorAgregator = $this->import->getErrorAggregator();
        $this->messageManager->addOperationResultMessages($errorAgregator, $this->import);

        return $result;
    }

    /**
     * Save entity
     *
     * @param array $rowData
     * @param null|string $type
     * @return bool
     */
    abstract public function saveEntity(array $rowData, ?string $type = null): bool;
}
