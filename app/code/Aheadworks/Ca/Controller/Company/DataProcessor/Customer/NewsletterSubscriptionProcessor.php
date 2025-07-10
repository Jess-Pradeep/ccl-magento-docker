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
namespace Aheadworks\Ca\Controller\Company\DataProcessor\Customer;

use Aheadworks\Ca\Controller\Company\DataProcessor\DataProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Stdlib\BooleanUtils;

/**
 * Class NewsletterSubscriptionProcessor
 *
 * @package Aheadworks\Ca\Controller\Company\DataProcessor\Customer
 */
class NewsletterSubscriptionProcessor implements DataProcessorInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param BooleanUtils $booleanUtils
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        BooleanUtils $booleanUtils,
        ArrayManager $arrayManager
    ) {
        $this->booleanUtils = $booleanUtils;
        $this->arrayManager = $arrayManager;
    }

    /**
     * Prepare post data for saving
     *
     * @param array $data
     * @return array
     */
    public function process($data)
    {
        $path = $this->arrayManager->findPath('is_subscribed', $data);

        if ($path) {
            $isSubscribed = $this->arrayManager->get($path, $data);
            $isSubscribed = $this->booleanUtils->toBoolean($isSubscribed);
            $data = $this->arrayManager->set($path, $data, $isSubscribed);
        }

        return $data;
    }
}
