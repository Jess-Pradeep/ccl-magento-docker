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
declare(strict_types=1);

namespace Aheadworks\Ca\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Third party manager
 */
class Manager
{
    /**
     * Aheadworks Cart To Quote module name
     */
    public const AW_CTQ_MODULE_NAME = 'Aheadworks_Ctq';

    /**
     * Aheadworks Store Credit module name
     */
    private const AW_STC_MODULE_NAME = 'Aheadworks_StoreCredit';

    /**
     * Aheadworks Reward Points module name
     */
    private const AW_RP_MODULE_NAME = 'Aheadworks_RewardPoints';

    /**
     * Aheadworks Payment Restrictions module name
     */
    private const AW_PAY_REST_MODULE_NAME = 'Aheadworks_PaymentRestrictions';

    /**
     * Aheadworks Shipping Restrictions module name
     */
    private const AW_SHIPPING_RESTRICTION_MODULE_NAME = 'Aheadworks_ShippingRestrictions';

    /**
     * Aheadworks Credit Limit module name
     */
    private const AW_CREDIT_LIMIT_MODULE_NAME = 'Aheadworks_CreditLimit';

    /**
     * Aheadworks Net 30 module name
     */
    private const AW_NET_30_MODULE_NAME = 'Aheadworks_Net30';

    /**
     * Msp ReCaptcha module name
     */
    private const MSP_RE_CAPTCHA = 'MSP_ReCaptcha';

    /**
     * Magento ReCaptchaCustomer module name
     */
    private const MAGENTO_RE_CAPTCHA_CUSTOMER = 'Magento_ReCaptchaCustomer';

    /**
     * Aheadworks Sarp2 module name
     */
    const AW_SARP2_MODULE_NAME = 'Aheadworks_Sarp2';

    /**
     * @var ModuleListInterface
     */
    private ModuleListInterface $moduleList;

    /**
     * @var array
     */
    private array $allModules = [
        self::AW_CTQ_MODULE_NAME,
        self::AW_STC_MODULE_NAME,
        self::AW_RP_MODULE_NAME,
        self::AW_PAY_REST_MODULE_NAME,
        self::AW_SHIPPING_RESTRICTION_MODULE_NAME,
        self::AW_CREDIT_LIMIT_MODULE_NAME,
        self::AW_NET_30_MODULE_NAME,
        self::AW_SARP2_MODULE_NAME
    ];

    /**
     * @param ModuleListInterface $moduleList
     * @param array $allModules
     */
    public function __construct(
        ModuleListInterface $moduleList,
        array $allModules = []
    ) {
        $this->moduleList = $moduleList;
        $this->allModules = array_merge($allModules, $this->allModules);
    }

    /**
     * Check if Aheadworks Cart To Quote module enabled
     *
     * @return bool
     */
    public function isAwCtqModuleEnabled(): bool
    {
        return $this->moduleList->has(self::AW_CTQ_MODULE_NAME);
    }

    /**
     * Check if Aheadworks Store Credit module enabled
     *
     * @return bool
     */
    public function isAwStoreCreditModuleEnabled(): bool
    {
        return $this->moduleList->has(self::AW_STC_MODULE_NAME);
    }

    /**
     * Check if Aheadworks Reward Points module enabled
     *
     * @return bool
     */
    public function isAwRewardPointsModuleEnabled(): bool
    {
        return $this->moduleList->has(self::AW_RP_MODULE_NAME);
    }

    /**
     * Check if Aheadworks Payment Restrictions module enabled
     *
     * @return bool
     */
    public function isAwPayRestModuleEnabled(): bool
    {
        return $this->moduleList->has(self::AW_PAY_REST_MODULE_NAME);
    }

    /**
     * Check if Aheadworks Shipping Restrictions module enabled
     *
     * @return bool
     */
    public function isAwShipRestModuleEnabled(): bool
    {
        return $this->moduleList->has(self::AW_SHIPPING_RESTRICTION_MODULE_NAME);
    }

    /**
     * Check if Aheadworks Credit Limit module enabled
     *
     * @return bool
     */
    public function isAwCreditLimitModuleEnabled(): bool
    {
        return $this->moduleList->has(self::AW_CREDIT_LIMIT_MODULE_NAME);
    }

    /**
     * Check if Aheadworks Net 30 module enabled
     *
     * @return bool
     */
    public function isAwNet30ModuleEnabled(): bool
    {
        return $this->moduleList->has(self::AW_NET_30_MODULE_NAME);
    }

    /**
     * Check if MSP ReCaptcha module enabled
     *
     * @return bool
     */
    public function isMspReCaptchaModuleEnabled(): bool
    {
        return $this->moduleList->has(self::MSP_RE_CAPTCHA);
    }

    /**
     * Check if Magento ReCaptchaCustomer module enabled
     *
     * @return bool
     */
    public function isMagentoReCaptchaCustomerModuleEnabled(): bool
    {
        return $this->moduleList->has(self::MAGENTO_RE_CAPTCHA_CUSTOMER);
    }

    /**
     * Check if module enabled by name
     *
     * @param string $name
     * @return bool
     */
    public function isModuleEnabledByName($name): bool
    {
        return $this->moduleList->has($name);
    }

    /**
     * Retrieve all third party modules
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->allModules;
    }
}
