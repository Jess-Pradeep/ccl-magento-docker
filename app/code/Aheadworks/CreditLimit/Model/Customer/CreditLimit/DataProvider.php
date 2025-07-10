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
namespace Aheadworks\CreditLimit\Model\Customer\CreditLimit;

use Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider\ProviderInterface;

/**
 * Class DataProvider
 *
 * @package Aheadworks\CreditLimit\Model\Customer\CreditLimit
 */
class DataProvider implements ProviderInterface
{
    /**
     * Credit limit field data scope
     */
    const CREDIT_LIMIT_DATA_SCOPE = 'aw_credit_limit';

    /**
     * @var array
     */
    private $providers;

    /**
     * @param array $providers
     */
    public function __construct(
        array $providers = []
    ) {
        $this->providers = $providers;
    }

    /**
     * @inheritdoc
     */
    public function getData($customerId, $websiteId)
    {
        $data = [];
        foreach ($this->providers as $provider) {
            $data = array_merge($data, $provider->getData($customerId, $websiteId));
        }

        return [
            self::CREDIT_LIMIT_DATA_SCOPE => $data
        ];
    }
}
