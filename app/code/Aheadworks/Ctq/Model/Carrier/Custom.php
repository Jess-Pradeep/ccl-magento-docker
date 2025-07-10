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
declare(strict_types=1);

namespace  Aheadworks\Ctq\Model\Carrier;

use Aheadworks\Ctq\Model\Config;
use Magento\Backend\Model\Auth\Session as BackendSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;

/**
 * Class Custom
 */
class Custom extends AbstractCarrier implements CarrierInterface
{
    public const CUSTOM_CARRIER = 'aw_ctq_custom';

    /**
     * @var float $amount
     */
    private float $amount = 0.0;

    /**
     * @var bool $changeAmount
     */
    private bool $changeAmount = false;

    protected $_code = 'aw_ctq_custom';

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param BackendSession $backendSession
     * @param Checker $rateChecker
     * @param Resolver $resolver
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        private readonly ResultFactory $rateResultFactory,
        private readonly MethodFactory $rateMethodFactory,
        private readonly BackendSession $backendSession,
        private readonly Checker $rateChecker,
        private readonly Resolver $resolver,
        private readonly Config $config,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     *  Collect rates
     *
     * @throws NoSuchEntityException
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->rateChecker->canAddCustomRate($request)){
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier(self::CUSTOM_CARRIER);
        $method->setCarrierTitle($this->config->getShippingTitle((int)$request->getStoreId()));

        $method->setMethod(self::CUSTOM_CARRIER);
        $method->setMethodTitle($this->getConfigData('name'));
        if ($this->backendSession->getUser() && $this->backendSession->getUser()->getId()) {
            $amount = $this->getAmount();
        } else {
            $amount = $this->resolver->getCurrentAmount($request);
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($amount);
        $method->setPrice($shippingPrice);
        $method->setCost($amount);

        $result->append($method);

        return $result;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     *  set amount
     *
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * Is change Amount
     *
     * @return bool
     */
    public function isChangeAmount(): bool
    {
        return $this->changeAmount;
    }

    /**
     * Set change amount
     *
     * @param bool $changeAmount
     */
    public function setChangeAmount(bool $changeAmount): void
    {
        $this->changeAmount = $changeAmount;
    }
}
