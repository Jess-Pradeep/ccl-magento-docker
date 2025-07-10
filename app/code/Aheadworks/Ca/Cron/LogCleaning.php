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

namespace Aheadworks\Ca\Cron;

use Aheadworks\Ca\Model\FlagFactory;
use Aheadworks\Ca\Model\Config;
use Aheadworks\Ca\Model\ResourceModel\HistoryLogFactory as HistoryLogResourceFactory;
use Magento\Store\Model\StoreManagerInterface;

class LogCleaning
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Flag model factory
     *
     * @var FlagFactory
     */
    private $flagFactory;

    /**
     * @var HistoryLogResourceFactory
     */
    private $historyLogResourceFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param HistoryLogResourceFactory $historyLogResourceFactory
     * @param Config $config
     * @param FlagFactory $_flagCode
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        HistoryLogResourceFactory $historyLogResourceFactory,
        Config $config,
        FlagFactory $flagFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->historyLogResourceFactory = $historyLogResourceFactory;
        $this->config = $config;
        $this->flagFactory = $flagFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Cron job for logs rotation
     *
     * @return void
     */
    public function execute(): void
    {
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        if ($this->config->isHistoryLogEnabled($websiteId) && (int)$this->config->getLifetime($websiteId)) {
            $lastLogCleaningFlag = $this->flagFactory->create()->loadSelf();
            $lastLogCleaningTime = $lastLogCleaningFlag->getFlagData();
            $frequencyLogCleaning = 3600 * 24 * (int)$this->config->getFrequencyLogCleaning($websiteId);
            if (!$lastLogCleaningTime || $lastLogCleaningTime < time() - $frequencyLogCleaning) {
                $this->historyLogResourceFactory->create()->cleanLog(
                    3600 * 24 * (int)$this->config->getLifetime($websiteId)
                );
            }
            $lastLogCleaningFlag->setFlagData(time())->save();
        }
    }
}
