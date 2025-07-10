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
 * @package    CtqGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CtqGraphQl\Model\DataProcessor\Quote\History;

use Aheadworks\Ctq\Model\Source\History\Action\Status;
use Aheadworks\Ctq\Model\Source\History\Action\Type;
use Aheadworks\CtqGraphQl\Model\DataProcessor\DataProcessorInterface;

class Actions implements DataProcessorInterface
{
    /**
     * @param Status $actionStatusSource
     * @param Type $actionTypeSource
     */
    public function __construct(
        private readonly Status $actionStatusSource,
        private readonly Type $actionTypeSource
    ) {
    }

    /**
     * Process data array
     *
     * @param array $data
     * @return array
     */
    public function process(array $data): array
    {
        foreach ($data as &$history) {
            if (isset($history['actions'])) {
                $history = $this->prepareActions($history);
            }
        }

        return $data;
    }

    /**
     * Prepare action
     *
     * @param array $value
     * @return array
     */
    public function prepareActions(array $value): array
    {
        foreach ($value['actions'] as &$action) {
            if ($action['status']) {
                $action = $this->prepareActionStatus($action);
            }
            if ($action['type']) {
                $action = $this->prepareActionType($action);
            }
            if (isset($action['actions'])) {
                $action['actions'] = $this->prepareActions($action);
            }
        }

        return $value;
    }

    /**
     * Prepare action status
     *
     * @param array|null $value
     * @return array
     */
    public function prepareActionStatus(?array $value): array
    {
        $options = $this->actionStatusSource->getOptionByCode($value['status']);
        $value['status'] = [
            'value' => $options['value'],
            'label' => $options['label']->getText() ?? '',
        ];

        return $value;
    }

    /**
     * Prepare action type
     *
     * @param array|null $value
     * @return array
     */
    public function prepareActionType(?array $value): array
    {
        $options = $this->actionTypeSource->getOptionByCode($value['type']);
        $value['type'] = [
            'value' => $options['value'],
            'label' => $options['label']->getText() ?? '',
        ];

        return $value;
    }
}
