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
namespace Aheadworks\Ca\Model\Email\Metadata\Builder\Modifier;

use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Magento\Framework\Exception\ConfigurationMismatchException;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;

/**
 * Class Composite
 *
 * @package Aheadworks\Ca\Model\Email\Metadata\Builder\Modifier
 */
class Composite implements ModifierInterface
{
    /**
     * @var ModifierInterface[]
     */
    private $modifierList;

    /**
     * @param ModifierInterface[] $modifierList
     */
    public function __construct(array $modifierList = [])
    {
        $this->modifierList = $modifierList;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface
    {
        foreach ($this->modifierList as $modifier) {
            if (!$modifier instanceof ModifierInterface) {
                throw new ConfigurationMismatchException(
                    __('Email metadata modifier must implement %1', ModifierInterface::class)
                );
            }
            $emailMetadata = $modifier->addMetadata($emailMetadata, $relatedObjectList);
        }

        return $emailMetadata;
    }
}
