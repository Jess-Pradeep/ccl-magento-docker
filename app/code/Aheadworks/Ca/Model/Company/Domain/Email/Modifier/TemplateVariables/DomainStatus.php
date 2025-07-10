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
namespace Aheadworks\Ca\Model\Company\Domain\Email\Modifier\TemplateVariables;

use Aheadworks\Ca\Api\Data\CompanyDomainInterface;
use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;
use Aheadworks\Ca\Model\Source\Company\EmailVariables;
use Aheadworks\Ca\Model\Source\Company\Domain\Status as DomainStatusSource;

/**
 * Class DomainStatus
 *
 * @package Aheadworks\Ca\Model\Company\Domain\Email\Modifier\TemplateVariables
 */
class DomainStatus implements ModifierInterface
{
    /**
     * @var DomainStatusSource
     */
    private $domainStatusSource;

    /**
     * @param DomainStatusSource $domainStatusSource
     */
    public function __construct(DomainStatusSource $domainStatusSource)
    {
        $this->domainStatusSource = $domainStatusSource;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        /** @var CompanyDomainInterface $newDomain */
        $newDomain = $relatedObjectList[ModifierInterface::DOMAIN];
        /** @var CompanyDomainInterface $oldDomain */
        $oldDomain = $relatedObjectList[ModifierInterface::OLD_DOMAIN];
        $templateVariables[EmailVariables::NEW_DOMAIN_STATUS] = (string)$this->domainStatusSource->getStatusLabel(
            $newDomain->getStatus()
        );
        $templateVariables[EmailVariables::PREVIOUS_DOMAIN_STATUS] = (string)$this->domainStatusSource->getStatusLabel(
            $oldDomain->getStatus()
        );
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
