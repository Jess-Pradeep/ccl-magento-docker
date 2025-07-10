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
 * @package    QuickOrder
 * @version    1.2.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\QuickOrder\Plugin\Model\AwGiftcard\Product\Option;

use Magento\Framework\DataObject;
use Aheadworks\QuickOrder\Model\ThirdPartyModule\BaseFactory;

class ProcessorPlugin
{
    /**
     * @param BaseFactory $giftcardConfigFactory
     */
    public function __construct(
        private BaseFactory $giftcardConfigFactory
    ) {}

    /**
     * Convert extra buy request params to product option
     *
     * @param \Aheadworks\Giftcard\Model\Product\Option\Processor $subject
     * @param array $resultArray
     * @param DataObject $buyRequest
     * @return array
     */
    public function afterConvertToProductOption($subject, $resultArray, $buyRequest)
    {
        if (isset($resultArray['aw_giftcard_option'])) {
            $resultArray = $this->prepareData($resultArray);
        }

        return $resultArray;
    }

    /**
     * Prepare Data
     *
     * @param array $data
     * @return array
     */
    private function prepareData(array $data): array
    {
        $giftcardConfigFactory = $this->giftcardConfigFactory->create();
        if ($giftcardConfigFactory) {
            $timezones = $giftcardConfigFactory->getGiftcardAllowedTimezones();
            if (count($timezones) === 1) {
                $data['aw_giftcard_option']->setAwGcDeliveryDateTimezone(
                    $data['aw_giftcard_option']->getAwGcDeliveryDateTimezone() ?: $timezones[0]
                );
            }
        }

        return $data;
    }
}
