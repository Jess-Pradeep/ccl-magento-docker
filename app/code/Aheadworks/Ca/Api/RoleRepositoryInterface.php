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
 * Interface RoleRepositoryInterface
 * @api
 */
interface RoleRepositoryInterface
{
    /**
     * Save role
     *
     * @param \Aheadworks\Ca\Api\Data\RoleInterface $role
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Ca\Api\Data\RoleInterface $role);

    /**
     * Retrieve role by id
     *
     * @param int $roleId
     * @param bool $reload returns non cached version of company
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($roleId, $reload = false);

    /**
     * Retrieve role by id
     *
     * @param int $companyId
     * @return \Aheadworks\Ca\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDefaultUserRole($companyId);

    /**
     * Retrieve role list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ca\Api\Data\RoleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
