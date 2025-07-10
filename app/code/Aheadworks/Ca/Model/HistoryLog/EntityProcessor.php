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

namespace Aheadworks\Ca\Model\HistoryLog;

use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Aheadworks\Ca\Model\HistoryLog\EntityProcessor\ProcessorInterface;

/**
 * Class EntityProcessor
 */
class EntityProcessor
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare entity before save
     *
     * @param HistoryLogInterface $object
     * @return HistoryLogInterface
     */
    public function prepareDataBeforeSave(HistoryLogInterface $object): HistoryLogInterface
    {
        foreach ($this->processors as $processor) {
            $processor->beforeSave($object);
        }
        return $object;
    }
}
