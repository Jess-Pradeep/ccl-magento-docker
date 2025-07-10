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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Model\Config\Backend;

use Magento\Framework\App\Config\Value as ConfigValue;
use Aheadworks\CreditLimit\Model\Config\Source\PaymentMethods as SourcePaymentMethods;

/**
 * Class PaymentMethods
 */
class PaymentMethods extends ConfigValue
{
    /**
     * Save only "all payments" variant if data for saving has it
     *
     * @return PaymentMethods
     */
    public function beforeSave(): PaymentMethods
    {
        $saveData = $this->getValue();
        if (is_array($saveData) && in_array(SourcePaymentMethods::ALL_METHODS_OPTION, $saveData)) {
            $this->setData('value', [SourcePaymentMethods::ALL_METHODS_OPTION]);
        }
        parent::beforeSave();

        return $this;
    }
}
