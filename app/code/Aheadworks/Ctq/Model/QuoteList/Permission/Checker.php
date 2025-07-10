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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\QuoteList\Permission;

use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context;
use Magento\Catalog\Model\Product;
use Aheadworks\Ctq\Api\BuyerPermissionManagementInterface;

/**
 * Class Checker
 *
 * @package Aheadworks\Ctq\Model\QuoteList\Permission
 */
class Checker
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var BuyerPermissionManagementInterface
     */
    private $buyerPermissionManagement;

    /**
     * @param HttpContext $httpContext
     * @param BuyerPermissionManagementInterface $buyerPermissionManagement
     */
    public function __construct(
        HttpContext $httpContext,
        BuyerPermissionManagementInterface $buyerPermissionManagement
    ) {
        $this->httpContext = $httpContext;
        $this->buyerPermissionManagement = $buyerPermissionManagement;
    }

    /**
     * Check is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        $customerGroupId = $this->httpContext->getValue(Context::CONTEXT_GROUP);
        $companyInfo = $this->httpContext->getValue('company_info');
        $isAllowedToQuote = $companyInfo['is_allowed_to_quote'] ?? null;
        $result = $this->buyerPermissionManagement->isAllowQuoteList($customerGroupId, null);

        if ($isAllowedToQuote !== null) {
            $result = $result && $isAllowedToQuote;
        }

        return $result;
    }

    /**
     * Check is allowed for product
     *
     * Extension point for plugins
     *
     * @param Product $product
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isAllowedForProduct($product)
    {
        return $this->isAllowed();
    }
}
