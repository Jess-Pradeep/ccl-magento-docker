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
 * Interface CompanyDomainRepositoryInterface
 * @api
 */
interface CompanyDomainRepositoryInterface
{
    /**
     * Save company domain
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyDomainInterface $domain
     * @return \Aheadworks\Ca\Api\Data\CompanyDomainInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Ca\Api\Data\CompanyDomainInterface $domain);

    /**
     * Retrieve company domain by ID
     *
     * @param int $domainId
     * @param bool $reload returns non cached version of domain
     * @return \Aheadworks\Ca\Api\Data\CompanyDomainInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($domainId, $reload = false);

    /**
     * Retrieve company domain list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ca\Api\Data\CompanyDomainSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete company domain
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyDomainInterface $domain
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Ca\Api\Data\CompanyDomainInterface $domain);

    /**
     * Delete company domain by ID
     *
     * @param int $domainId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($domainId);
}
