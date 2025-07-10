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

namespace Aheadworks\Ca\Model\HistoryLog\MessagesProcessor;

use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Magento\Framework\Model\AbstractModel;

interface ProcessorInterface
{
    /**
     * Process data before save
     *
     * @param HistoryLogInterface $object
     * @param array $processor
     * @param AbstractModel $model
     * @return HistoryLogInterface
     */
    public function addCustomData(HistoryLogInterface $object, array $processor, AbstractModel $model): HistoryLogInterface;
}
