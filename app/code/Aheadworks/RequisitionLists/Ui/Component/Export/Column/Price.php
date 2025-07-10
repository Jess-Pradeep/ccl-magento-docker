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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RequisitionLists\Ui\Component\Export\Column;

use Aheadworks\RequisitionLists\Model\Product\DetailProvider\Pool;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Pricing\PriceCurrencyInterface;

class Price extends Column
{
    /**
     * @var RendererPool
     */
    private RendererPool $rendererPool;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Pool $pool
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly Pool $pool,
        private readonly PriceCurrencyInterface $priceCurrency,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws LocalizedException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item['price'] = $this->getPrice($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get price html
     *
     * @param array $item
     * @return float
     * @throws LocalizedException
     */
    private function getPrice(array $item): float
    {
        $provider = $this->pool->getProvider($item);
        $price = $provider->getFinalPriceForBuyRequest();

        if ($price) {
            $price = $this->priceCurrency->convert($price);
        }

        return (float)($price
            ?? $provider->getProduct()?->getPriceInfo()?->getPrice(FinalPrice::PRICE_CODE)?->getValue()
            ?? 0);
    }
}
