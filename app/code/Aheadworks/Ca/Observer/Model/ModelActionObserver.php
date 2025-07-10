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

namespace Aheadworks\Ca\Observer\Model;

use Aheadworks\Ca\Model\Config;
use Aheadworks\Ca\Model\HistoryLog\Checker\Models as ModelsChecker;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Aheadworks\Ca\Model\Service\HistoryLogServiceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class ModelActionObserver implements ObserverInterface
{
    /**
     * @var HistoryLogServiceFactory
     */
    private $historyLogServiceFactory;

    /**
     * @var ModelsChecker
     */
    private $modelsChecker;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param HistoryLogServiceFactory $historyLogServiceFactory
     * @param ModelsChecker $modelsChecker
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        HistoryLogServiceFactory $historyLogServiceFactory,
        ModelsChecker $modelsChecker,
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->historyLogServiceFactory = $historyLogServiceFactory;
        $this->modelsChecker = $modelsChecker;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        $websiteId = (int)$this->storeManager->getStore()->getWebsiteId();
        if ($this->config->isHistoryLogEnabled($websiteId)) {
            $resourceName = $observer->getEvent()->getObject()->getResourceName();
            if ($this->modelsChecker->checkModelBelongToAllowedModelsList($resourceName)) {
                $historyLogService = $this->historyLogServiceFactory->create();
                $historyLogService->addHistoryLog($observer->getEvent()->getName(), $observer->getEvent()->getObject());
            }
        }
    }
}
