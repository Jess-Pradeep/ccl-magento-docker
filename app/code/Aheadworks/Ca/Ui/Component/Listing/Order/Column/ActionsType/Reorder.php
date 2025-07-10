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

use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\UrlInterface;
use Magento\Sales\Helper\Reorder as HelperReorder;

/**
 * Class Reorder
 */
class Reorder
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var PostHelper
     */
    private $postHelper;

    /**
     * @var HelperReorder
     */
    private $helperReorder;

    /**
     * @param UrlInterface $urlBuilder
     * @param PostHelper $postHelper
     */
    public function __construct(
        UrlInterface $urlBuilder,
        PostHelper $postHelper,
        HelperReorder $helperReorder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->postHelper = $postHelper;
        $this->helperReorder = $helperReorder;
    }

    /**
     * Get reorder link URL
     *
     * @param int $orderId
     * @return array|null
     */
    public function getLink(int $orderId): ?array
    {
        $link = null;
        if ($this->helperReorder->canReorder($orderId)) {
            $link = [
                'href' => '#',
                'postData' => $this->postHelper->getPostData($this->getReorderUrl($orderId)),
                'label' => __('Reorder')
            ];
        }

        return $link;
    }

    /**
     * Get reorder URL
     *
     * @param int $orderId
     * @return string
     */
    private function getReorderUrl(int $orderId): string
    {
        return $this->urlBuilder->getUrl('sales/order/reorder', ['order_id' => $orderId]);
    }
}
