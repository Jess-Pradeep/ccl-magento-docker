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

namespace Aheadworks\Ca\Ui\Component\Listing\Order\Column\ActionsType;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;

class View
{
    public function __construct(
        private readonly UrlInterface $urlBuilder,
        private readonly AppState $appState
    ) {
    }

    /**
     * Get view link URL
     *
     * @param int $orderId
     * @return array
     * @throws LocalizedException
     */
    public function getLink(int $orderId): array
    {
        $link = [
            'href' => $this->getViewUrl($orderId),
            'label' => __($this->appState->getAreaCode() === Area::AREA_FRONTEND ? 'View Order' : 'View')
        ];

        return $link;
    }

    /**
     * Get order view URL
     *
     * @param int $orderId
     * @return string
     */
    private function getViewUrl(int $orderId): string
    {
        return $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $orderId]);
    }
}
