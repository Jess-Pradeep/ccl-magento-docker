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
namespace Aheadworks\Ca\Model\Email\Metadata\Builder;

use Magento\Framework\Exception\ConfigurationMismatchException;

/**
 * Class ModifierPool
 *
 * @package Aheadworks\Ca\Model\Email\Metadata\Builder
 */
class ModifierPool
{
    /**
     * @var ModifierInterface[]
     */
    private $modifierList;

    /**
     * @param ModifierInterface[] $modifierList
     */
    public function __construct(
        array $modifierList = []
    ) {
        $this->modifierList = $modifierList;
    }

    /**
     * Retrieve metadata modifier for specific email notification type
     *
     * @param string $notificationType
     * @return ModifierInterface
     * @throws ConfigurationMismatchException
     */
    public function getModifierByNotificationType($notificationType)
    {
        if (!isset($this->modifierList[$notificationType])) {
            throw new ConfigurationMismatchException(
                __('Unknown email metadata modifier for notification: %1 requested', ModifierInterface::class)
            );
        }

        return $this->modifierList[$notificationType];
    }
}
