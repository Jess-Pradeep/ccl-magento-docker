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

namespace Aheadworks\Ca\Model\Service;

use Aheadworks\Ca\Model\HistoryLog\ManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Ca\Api\Data\HistoryLogInterfaceFactory;
use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Aheadworks\Ca\Api\HistoryLogRepositoryInterface;
use Aheadworks\Ca\Model\HistoryLog\MessagesProcessor;

/**
 * Class HistoryLogService
 */
class HistoryLogService implements ManagementInterface
{
    /**
     * @var HistoryLogInterfaceFactory
     */
    private $historyLogInterfaceFactory;

    /**
     * @var HistoryLogRepositoryInterface
     */
    private $historyLogRepository;

    /**
     * @var MessagesProcessor
     */
    private $processor;

    /**
     * @param HistoryLogRepositoryInterface $historyLogRepository
     * @param HistoryLogInterfaceFactory $historyLogInterfaceFactory
     * @param MessagesProcessor $processor
     */
    public function __construct(
        HistoryLogRepositoryInterface $historyLogRepository,
        HistoryLogInterfaceFactory $historyLogInterfaceFactory,
        MessagesProcessor $processor
    ) {
        $this->historyLogRepository = $historyLogRepository;
        $this->historyLogInterfaceFactory = $historyLogInterfaceFactory;
        $this->processor = $processor;
    }

    /**
     * Add History Log
     *
     * @param string $eventName
     * @param AbstractModel $model
     * @return void
     * @throws LocalizedException
     */
    public function addHistoryLog(string $eventName, AbstractModel $model): void
    {
        $historyLog = $this->processor->prepareDataBeforeSave(
            $this->historyLogInterfaceFactory->create(),
            $model,
            $eventName
        );
        if ($historyLog->getEntityId() && $historyLog->getPerformedAction()) {
            $this->historyLogRepository->save($historyLog);
        }
    }

    /**
     * Add History Log of deleted model
     *
     * @param HistoryLogInterface $historyLog
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addHistoryLogDeletedModel(HistoryLogInterface $historyLog): void
    {
        $this->historyLogRepository->save($historyLog);
    }
}
