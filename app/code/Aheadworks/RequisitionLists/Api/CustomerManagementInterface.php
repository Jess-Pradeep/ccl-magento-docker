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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RequisitionLists\Api;

/**
 * Interface CustomerManagementInterface
 * @api
 */
interface CustomerManagementInterface
{
    /**
     * Check if requisition lists is active for current website
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isActiveForCurrentWebsite($websiteId = null);

    /**
     * Check config by name for current website
     *
     * @param string $name
     * @param null $websiteId
     * @return bool
     */
    public function isActiveForCurrentWebsiteByName($name, $websiteId = null);

    /**
     * Check if requisition lists is show in order view page for current website
     *
     * @param null $websiteId
     * @return bool
     */
    public function isShowInOrderPageForCurrentWebsite($websiteId = null);

    /**
     * Check if requisition lists is show in catalog for current website
     *
     * @param null $websiteId
     * @return bool
     */
    public function isShowInCatalogForCurrentWebsite($websiteId = null);

    /**
     * Check if requisition lists is show in cart page for current website
     *
     * @param null $websiteId
     * @return bool
     */
    public function isShowInCartPageForCurrentWebsite($websiteId = null);
}
