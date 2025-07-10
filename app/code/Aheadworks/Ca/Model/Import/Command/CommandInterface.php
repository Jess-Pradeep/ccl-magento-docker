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

use Aheadworks\Ca\Model\Import\ImportEntity;

/**
 * Interface to execute command
 */
interface CommandInterface
{
    /**
     * Executes the current command
     *
     * @param array $bunch
     * @param ImportEntity $importEntity
     * @return void
     */
    public function execute(array $bunch, ImportEntity $importEntity): void;
}
