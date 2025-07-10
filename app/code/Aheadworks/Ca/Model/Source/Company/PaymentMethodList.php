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
namespace Aheadworks\Ca\Model\Source\Company;

/**
 * Class PaymentMethodList
 * @package Aheadworks\Ca\Model\Source\Company
 */
class PaymentMethodList extends PaymentMethod
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        if ($this->thirdPartyModuleManager->isAwPayRestModuleEnabled()) {
            $payments = $this->getPaymentManagement()->getPaymentMethods();
            $options = $this->getPaymentOptions($payments);
        }

        return $options;
    }
}
