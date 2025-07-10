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
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;
use Magento\Framework\App\Area;

/**
 * Class TemplateOptions
 *
 * @package Aheadworks\Ca\Model\Email\Metadata\Builder\Modifier
 */
class TemplateOptions implements ModifierInterface
{
    /**
     * @inheritdoc
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface
    {
        $emailMetadata->setTemplateOptions(
            [
                'area' => Area::AREA_FRONTEND,
                'store' => $relatedObjectList[ModifierInterface::STORE_ID] ?? null,
            ]
        );

        return $emailMetadata;
    }
}
