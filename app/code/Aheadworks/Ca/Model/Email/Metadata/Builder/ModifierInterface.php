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

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Ca\Model\Email\EmailMetadataInterface;

/**
 * Interface ModifierInterface
 *
 * @package Aheadworks\Ca\Model\Email\Metadata\Builder
 */
interface ModifierInterface
{
    /**#@+
     * Constants defined for the keys of $relatedObjectList array
     */
    const DOMAIN = 'domain';
    const OLD_DOMAIN = 'old_domain';
    const COMPANY = 'company';
    const ORDER = 'order';
    const CUSTOMER = 'customer';
    const STORE_ID = 'store_id';
    /**#@-*/

    /**
     * Add metadata to existing object
     *
     * @param EmailMetadataInterface $emailMetadata
     * @param array $relatedObjectList
     * @return EmailMetadataInterface
     * @throws LocalizedException
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface;
}
