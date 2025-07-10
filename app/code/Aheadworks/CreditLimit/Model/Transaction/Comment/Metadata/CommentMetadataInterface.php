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
namespace Aheadworks\CreditLimit\Model\Transaction\Comment\Metadata;

/**
 * Interface CommentMetadataInterface
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Comment\Metadata
 */
interface CommentMetadataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const TYPE = 'type';
    const PLACEHOLDER = 'placeholder';
    /**#@-*/

    /**
     * Get comment type
     *
     * @return string
     */
    public function getType();

    /**
     * Get comment placeholder
     *
     * @return string
     */
    public function getPlaceholder();
}
