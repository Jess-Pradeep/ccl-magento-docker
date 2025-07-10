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

namespace Aheadworks\Ca\Model\Customer\CompanyUser\Email\Modifier\TemplateId;

use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;
use Aheadworks\Ca\Model\Config;

class NewCompanyAdminAssignedForCompanyAdmin implements ModifierInterface
{
    /**
     * @param Config $config
     */
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * Add metadata to existing object
     *
     * @param EmailMetadataInterface $emailMetadata
     * @param array $relatedObjectList
     * @return EmailMetadataInterface
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface
    {
        $storeId = (int)$relatedObjectList[ModifierInterface::STORE_ID];
        $emailMetadata->setTemplateId($this->config->getNewCompanyAdminAssignedForCompanyAdminTemplate($storeId));

        return $emailMetadata;
    }
}
