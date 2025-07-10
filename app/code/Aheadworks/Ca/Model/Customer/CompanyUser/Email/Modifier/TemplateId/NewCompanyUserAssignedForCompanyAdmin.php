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
namespace Aheadworks\Ca\Model\Customer\CompanyUser\Email\Modifier\TemplateId;

use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;
use Aheadworks\Ca\Model\Config;

/**
 * Class NewCompanyUserAssignedForCompanyAdmin
 *
 * @package Aheadworks\Ca\Model\Customer\CompanyUser\Email\Modifier\TemplateId
 */
class NewCompanyUserAssignedForCompanyAdmin implements ModifierInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface
    {
        $storeId = $relatedObjectList[ModifierInterface::STORE_ID];
        $emailMetadata->setTemplateId($this->config->getNewCompanyUserAssignedForCompanyAdminTemplate($storeId));

        return $emailMetadata;
    }
}
