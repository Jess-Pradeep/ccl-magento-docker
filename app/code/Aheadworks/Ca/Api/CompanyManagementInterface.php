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
namespace Aheadworks\Ca\Api;

/**
 * Interface CompanyManagementInterface
 * @api
 */
interface CompanyManagementInterface
{
    /**
     * Remove company and all users associated to it
     *
     * @param int $companyId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeCompany($companyId);
}
