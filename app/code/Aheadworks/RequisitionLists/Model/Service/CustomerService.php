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
namespace Aheadworks\RequisitionLists\Model\Service;

use Aheadworks\RequisitionLists\Api\CustomerManagementInterface;
use Aheadworks\RequisitionLists\Model\Config;

/**
 * Class CustomerService
 */
class CustomerService implements CustomerManagementInterface
{
    /**
     * @var Config
     */
    private $config;


    /**
     * @param Config $config
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function isActiveForCurrentWebsite($websiteId = null)
    {
        return $this->config->isEnabled($websiteId);
    }

    /**
     * @inheritdoc
     */
    public function isActiveForCurrentWebsiteByName($name, $websiteId = null)
    {
        return $this->config->getGeneralConfigByName($name, $websiteId);
    }

    /**
     * @inheritdoc
     */
    public function isShowInOrderPageForCurrentWebsite($websiteId = null)
    {
        return $this->config->isShowInOrderPage($websiteId);
    }

    /**
     * @inheritdoc
     */
    public function isShowInCatalogForCurrentWebsite($websiteId = null)
    {
        return $this->config->isShowInCatalog($websiteId);
    }

    /**
     * @inheritdoc
     */
    public function isShowInCartPageForCurrentWebsite($websiteId = null)
    {
        return $this->config->isShowInCartPage($websiteId);
    }
}
