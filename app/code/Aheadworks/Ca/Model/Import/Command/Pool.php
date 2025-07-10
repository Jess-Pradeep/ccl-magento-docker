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

namespace Aheadworks\Ca\Model\Import\Command;

use Magento\Framework\Exception\ConfigurationMismatchException;

/**
 * Command pool
 */
class Pool
{
    /**
     * @param CommandInterface[] $commands
     */
    public function __construct(
        private readonly array $commands = []
    ) {
    }

    /**
     * Retrieve command instance
     *
     * @param string $behavior
     * @return CommandInterface
     * @throws ConfigurationMismatchException
     */
    public function getCommand(string $behavior): CommandInterface
    {
        if (!isset($this->commands[$behavior])) {
            throw new ConfigurationMismatchException(
                __('There is no command registered for behavior "%1".', $behavior)
            );
        }

        return $this->commands[$behavior];
    }
}
