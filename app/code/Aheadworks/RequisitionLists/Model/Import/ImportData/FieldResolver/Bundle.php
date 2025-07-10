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

namespace Aheadworks\RequisitionLists\Model\Import\ImportData\FieldResolver;

use Aheadworks\RequisitionLists\Model\Import\ImportData\FieldResolverInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Bundle\Model\Product\OptionList;

class Bundle implements FieldResolverInterface
{
    /**
     * @var string
     */
    private string $parentSku = '';

    /**
     * @var array
     */
    private array $optionIds = [];

    /**
     * @param OptionList $optionList
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        private readonly OptionList $optionList,
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * @var array|string[]
     */
    private array $fieldsMap = [
        'bundle_option'
    ];

    /**
     * @var array|string[]
     */
    private array $transformToArrayFieldsMap = [
        'bundle_option_qty'
    ];

    /**
     * Resolve bundle data
     *
     * @param string $data
     * @param string $field
     * @return array
     */
    public function resolveData(string $data, string $field): array
    {
        $result = [];

        if ($field === 'sku') {
            $this->parentSku = $data;
        }

        try {
            if (in_array($field, $this->fieldsMap)) {
                $bundleProduct = $this->productRepository->get($this->parentSku);
                $optionList = $this->optionList->getItems($bundleProduct);

                foreach ($optionList as $option) {
                    foreach ($option->getProductLinks() as $link) {
                        foreach (explode(',', $data) ?? [] as $value) {
                            if ($value === $link->getSku()) {
                                $result[$option->getOptionId()] = $link->getId();
                                $this->optionIds[$option->getOptionId()] = 0;
                            }
                        }
                    }
                }
            }

            if (in_array($field, $this->transformToArrayFieldsMap)) {
                $qtyArr = explode(',', $data) ?? [];
                $index = 0;

                foreach ($this->optionIds as &$qty) {
                    $qty = $qtyArr[$index];
                    $index++;
                }
                $result = $this->optionIds;
            }
        } catch (\Exception|\Error) {
        }

        return $result;
    }
}
