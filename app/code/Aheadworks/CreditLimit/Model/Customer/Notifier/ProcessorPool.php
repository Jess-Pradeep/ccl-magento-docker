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
namespace Aheadworks\CreditLimit\Model\Customer\Notifier;

/**
 * Class ProcessorPool
 *
 * @package Aheadworks\CreditLimit\Model\Customer\Notifier
 */
class ProcessorPool
{
    /**
     * @var EmailProcessorInterface[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(
        array $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * Retrieve customer email processor
     *
     * @param string $type
     * @return EmailProcessorInterface|bool
     */
    public function get($type)
    {
        if (!isset($this->processors[$type])) {
            return false;
        }

        if (!$this->processors[$type] instanceof EmailProcessorInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Email processor does not implement required interface: %s.',
                    EmailProcessorInterface::class
                )
            );
        }

        return $this->processors[$type];
    }
}
