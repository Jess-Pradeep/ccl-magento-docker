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

namespace Aheadworks\Ca\Model\Company\Address\Shared;

use Aheadworks\Ca\Model\Company\Address\Shared\Action\Processor\ProcessorInterface;

class Pool
{
    /**
     * @param ProcessorInterface|[] $processor
     */
    public function __construct(
        private array $processors = []
    ) {
    }

    /**
     * Retrieve command instance
     *
     * @param string $action
     * @return ProcessorInterface
     */
    public function getProcessor(string $action): ProcessorInterface
    {
        if (isset($this->processors[$action])) {
            if (!$this->processors[$action] instanceof ProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Action processor does not implement required interface: %s.',
                        ProcessorInterface::class
                    )
                );
            }

            return $this->processors[$action];
        }

        throw new \InvalidArgumentException(
            sprintf('Action processor is not found for job type: %s.', $action)
        );
    }
}
