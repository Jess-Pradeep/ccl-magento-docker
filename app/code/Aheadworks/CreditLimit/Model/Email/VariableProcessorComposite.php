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
namespace Aheadworks\CreditLimit\Model\Email;

/**
 * Class VariableProcessorComposite
 *
 * @package Aheadworks\CreditLimit\Model\Email
 */
class VariableProcessorComposite implements VariableProcessorInterface
{
    /**
     * @var VariableProcessorInterface[]
     */
    private $processors;

    /**
     * @param VariableProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Prepare variables
     *
     * @param array $variables
     * @return array
     */
    public function prepareVariables($variables)
    {
        foreach ($this->processors as $processor) {
            if (!$processor instanceof VariableProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Variable processor does not implement required interface: %s.',
                        VariableProcessorInterface::class
                    )
                );
            }
            $variables = $processor->prepareVariables($variables);
        }
        return $variables;
    }
}
