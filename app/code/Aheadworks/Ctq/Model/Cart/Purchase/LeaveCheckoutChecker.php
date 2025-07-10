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
namespace Aheadworks\Ctq\Model\Cart\Purchase;

use Magento\Framework\App\RequestInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;

class LeaveCheckoutChecker
{
    /**
     * @var array
     */
    private $allow = [];

    /**
     * @var array
     */
    private $disallow = [];

    /**
     * @var array
     */
    private $allowedRequestPaths = [];

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param array $allow
     * @param array $disallow
     * @param array $allowedRequestPaths
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        array $allow = [],
        array $disallow = [],
        array $allowedRequestPaths = []
    ) {
        $this->cartRepository = $cartRepository;
        $this->allow = $allow;
        $this->disallow = $disallow;
        $this->allowedRequestPaths = $allowedRequestPaths;
    }

    /**
     * Check if customer leave checkout
     *
     * @param Quote $cart
     * @param RequestInterface $request
     * @return bool
     */
    public function isLeave($cart, $request)
    {
        $result = false;

        foreach ($this->allowedRequestPaths as $allowedRequestPath) {
            if (strpos($request->getPathInfo(), $allowedRequestPath) !== false) {
                return false;
            }
        }

        if ($cart->getIsActive()
            && $cart->getExtensionAttributes()
            && $cart->getExtensionAttributes()->getAwCtqQuote()
        ) {
            $result = true;
            $module = $request->getModuleName();
            $controller = $request->getControllerName();
            $action = $request->getActionName();

            if ($this->isInAllowList($module, $controller, $action)) {
                $result = false;
            }
            if ($this->isInDisallowList($module, $controller, $action)) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Check if in allow list
     *
     * @param string|null $module
     * @param string|null $controller
     * @param string|null $action
     * @return bool
     */
    private function isInAllowList($module, $controller, $action)
    {
        foreach ($this->allow as $exclusion) {
            if ($module == $exclusion['module']
                && ($controller == $exclusion['controller'] || '*' == $exclusion['controller'])
                && ($action == $exclusion['action'] || '*' == $exclusion['action'])
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if in disallow list
     *
     * @param string|null $module
     * @param string|null $controller
     * @param string|null $action
     * @return bool
     */
    private function isInDisallowList($module, $controller, $action)
    {
        foreach ($this->disallow as $exclusion) {
            if ($module == $exclusion['module']
                && ($controller == $exclusion['controller'] || '*' == $exclusion['controller'])
                && ($action == $exclusion['action'] || '*' == $exclusion['action'])
            ) {
                return true;
            }
        }

        return false;
    }
}
