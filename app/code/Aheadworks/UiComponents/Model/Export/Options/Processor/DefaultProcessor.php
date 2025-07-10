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

namespace Aheadworks\UiComponents\Model\Export\Options\Processor;

use Aheadworks\UiComponents\Model\Export\Options\ConfigProcessorInterface;

class DefaultProcessor implements ConfigProcessorInterface
{
    /**
     * @param array $pathes
     */
    public function __construct(
        private readonly array $pathes = []
    ) {
    }

    /**
     * Default prepare options
     *
     * @param array $config
     * @return array
     */
    public function prepare(array $config): array
    {
        $options = [];

        foreach ($config['options'] ?? [] as $key => $option) {
            if (key_exists($key, $this->pathes)) {
                $option['url'] = $this->pathes[$key];
                $options[] = $option;
            }
        }

        $config['options'] = $options;

        return $config;
    }
}
