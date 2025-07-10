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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\Ctq\ViewModel\History;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Ctq\Api\HistoryRepositoryInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterfaceFactory;

/**
 * Provides history objects
 */
class Provider implements ArgumentInterface
{
    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var HistoryInterfaceFactory
     */
    private $historyFactory;

    /**
     * @param HistoryInterfaceFactory $historyFactory
     * @param HistoryRepositoryInterface $historyRepository
     */
    public function __construct(
        HistoryInterfaceFactory $historyFactory,
        HistoryRepositoryInterface $historyRepository
    ) {
        $this->historyFactory = $historyFactory;
        $this->historyRepository = $historyRepository;
    }

    /**
     * Get history item
     *
     * @param int $historyId
     * @return HistoryInterface
     * @throws LocalizedException
     */
    public function getHistoryItem(int $historyId) : HistoryInterface
    {
        return $this->historyRepository->get($historyId);
    }

    /**
     * Get empty history item
     *
     * @return HistoryInterface
     * @throws LocalizedException
     */
    public function getEmptyHistoryItem() : HistoryInterface
    {
        return $this->historyFactory->create();
    }
}
