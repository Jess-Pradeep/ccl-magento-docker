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
namespace Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider;

use Aheadworks\CreditLimit\Model\Website\CurrencyList;
use Magento\Framework\Locale\CurrencyInterface;

/**
 * Class CreditLimit
 *
 * @package Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider
 */
class AllowedWebsiteCurrencyList implements ProviderInterface
{
    /**
     * @var CurrencyList
     */
    private $currencyList;

    /**
     * @var CurrencyInterface
     */
    private $currency;

    /**
     * @param CurrencyList $currencyList
     * @param CurrencyInterface $currency
     */
    public function __construct(
        CurrencyList $currencyList,
        CurrencyInterface $currency
    ) {
        $this->currencyList = $currencyList;
        $this->currency = $currency;
    }

    /**
     * @inheritdoc
     */
    public function getData($customerId, $websiteId)
    {
        $options = [];
        $listOfCurrencyCodes = $this->currencyList->getAllowedCurrenciesForWebsite($websiteId);
        foreach ($listOfCurrencyCodes as $currencyCode) {
            $currency = $this->currency->getCurrency($currencyCode);
            $options[] = [
                'value' => $currencyCode,
                'label' => $currency->getName()
            ];
        }
        $data['allowedCurrencyList'] = $options;
        $data['baseCurrency'] = $this->currencyList->getBaseCurrencyForWebsite($websiteId);

        return $data;
    }
}
