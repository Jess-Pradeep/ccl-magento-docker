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

/**
 * Class Composite
 */
class Composite implements ImportProcessorInterface
{
    /**
     * Composite constructor.
     *
     * @param array $processors
     * @param array $defaultImportConfig
     */
    public function __construct(
        private array $processors = [],
        private array $defaultImportConfig = []
    ) {
    }

    /**
     * Run import
     *
     * @param array $data
     * @return bool
     */
    public function perform(array $data): bool
    {
        $result = false;
        $type = $data['entity'] ?? null;

        if ($this->isAllowedRunProcessor($type)) {
            $data = array_merge($data, $this->defaultImportConfig);
            $result = $this->processors[$type]->perform($data);
        }

        return $result;
    }

    /**
     * Save entity
     *
     * @param array $rowData
     * @param string|null $type
     * @return bool
     */
    public function saveEntity(array $rowData, ?string $type = null): bool
    {
        $result = false;

        if ($this->isAllowedRunProcessor($type)) {
            $result = $this->processors[$type]->saveEntity($rowData);
        }

        return $result;
    }

    /**
     * Check is exist processor
     *
     * @param string $type
     * @return bool
     */
    private function isExistProcessor(string $type): bool
    {
        return $type && isset($this->processors[$type]);
    }

    /**
     * Check is allowed run processor
     *
     * @param string $type
     * @return bool
     */
    private function isAllowedRunProcessor(string $type): bool
    {
        return $this->isExistProcessor($type) && $this->processors[$type] instanceof ImportProcessorInterface;
    }
}
