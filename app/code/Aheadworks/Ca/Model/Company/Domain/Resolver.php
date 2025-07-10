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
namespace Aheadworks\Ca\Model\Company\Domain;

/**
 * Class Resolver
 *
 * @package Aheadworks\Ca\Model\Company\Domain
 */
class Resolver
{
    /**
     * Resolve domain name from email
     *
     * @param string $email
     * @return bool|string
     */
    public function resolveFromEmail($email)
    {
        return substr(strrchr($email, "@"), 1);
    }
}
