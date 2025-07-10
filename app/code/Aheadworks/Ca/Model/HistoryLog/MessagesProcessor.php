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
use Aheadworks\Ca\Model\HistoryLog\MessagesProcessor\ProcessorInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class MessagesProcessor
 */
class MessagesProcessor
{
    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(private readonly array $processors = [])
    {
    }

    /**
     * Prepare entity before save
     *
     * @param HistoryLogInterface $object
     * @param AbstractModel $model
     * @param string $eventName
     * @return HistoryLogInterface
     */
    public function prepareDataBeforeSave(
        HistoryLogInterface $object,
        AbstractModel $model,
        string $eventName
    ): HistoryLogInterface {
        $processors = $this->processors[$eventName] ?? [];
        foreach ($processors as $processor) {
            if ($processor['resource_name'] == $model->getResourceName()) {
                $object->setEntityType($processor['entity_type']);
                $processor['object']->addCustomData($object, $processor, $model);
            }
        }
        return $object;
    }
}
