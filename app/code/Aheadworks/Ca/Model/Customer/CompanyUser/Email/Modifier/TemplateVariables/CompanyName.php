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
namespace Aheadworks\Ca\Model\Customer\CompanyUser\Email\Modifier\TemplateVariables;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;
use Aheadworks\Ca\Model\Source\Customer\CompanyUser\EmailVariables;

/**
 * Class CompanyName
 *
 * @package Aheadworks\Ca\Model\Customer\CompanyUser\Email\Modifier\TemplateVariables
 */
class CompanyName implements ModifierInterface
{
    /**
     * @inheritDoc
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        /** @var CompanyInterface $company */
        $company = $relatedObjectList[ModifierInterface::COMPANY];
        $templateVariables[EmailVariables::COMPANY_NAME] = $company->getName();
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
