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
 * Interface CompanyDomainManagementInterface
 * @api
 */
interface CompanyDomainManagementInterface
{
    /**
     * Create company domain
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyDomainInterface $domain
     * @param string $requestedBy backend admin or frontend company admin
     * @return \Aheadworks\Ca\Api\Data\CompanyDomainInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createDomain($domain, $requestedBy);

    /**
     * Update company domain
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyDomainInterface $domain
     * @param string $requestedBy backend admin or frontend company admin
     * @return \Aheadworks\Ca\Api\Data\CompanyDomainInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateDomain($domain, $requestedBy);

    /**
     * Delete company domain
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyDomainInterface $domain
     * @param string $requestedBy backend admin or frontend company admin
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteDomain($domain, $requestedBy);
}
