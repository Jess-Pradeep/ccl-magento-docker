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
 * @package    UiComponents
 * @version    1.0.5
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\UiComponents\Model\Export\Options;

class ConfigProcessor implements ConfigProcessorInterface
{
    /**
     * @param ConfigProcessorInterface[] $processors
     */
    public function __construct(
          private array $processors = []
    ) {
    }

    /**
     * Prepare options for export
     *
     * @param array $config
     * @return array
     */
    public function prepare(array $config): array
    {
        foreach ($this->processors as $processor) {
            if ($processor instanceof ConfigProcessorInterface) {
                $config = $processor->prepare($config);
            }
        }

        return $config;
    }
}
