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

namespace Aheadworks\QuickOrder\Model\Product\Option\CustomOptions\File;

use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Magento\Catalog\Model\Product\Exception;
use Magento\Framework\DataObject;
use Aheadworks\QuickOrder\Api\ProductListItemRepositoryInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\CustomOptions\CustomOptionFactory;
use Magento\Catalog\Model\Product\Option\Type\File\ValidatorFile;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\QuickOrder\Api\Data\CustomOptionFileInterface;
use Aheadworks\QuickOrder\Api\Data\CustomOptionFileInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\CustomOptionInterface;

class OptionBuilder
{
    /**
     * @param ProductListItemRepositoryInterface $productListItemRepository
     * @param CustomOptionFactory $customOptionFactory
     * @param ValidatorFile $validatorFile
     * @param ProductRepositoryInterface $productRepository
     * @param CustomOptionFileInterfaceFactory $customOptionFileInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        private ProductListItemRepositoryInterface $productListItemRepository,
        private CustomOptionFactory $customOptionFactory,
        private ValidatorFile $validatorFile,
        private ProductRepositoryInterface $productRepository,
        private CustomOptionFileInterfaceFactory $customOptionFileInterfaceFactory,
        private DataObjectHelper $dataObjectHelper
    ) {}

    /**
     * Build file options
     *
     * @param DataObject $request
     * @return array
     */
    public function buildFileOptions(DataObject $request): array
    {
        $options = [];
        if ($fileOptionIds = $this->getFileOptionIds($request)) {
            $itemKey = $request->getData(ProductListItemInterface::ITEM_KEY);
            try {
                $item = $this->productListItemRepository->getByKey($itemKey);
                $product = $this->productRepository->get($item->getProductSku());
            } catch (NoSuchEntityException $e) {
                return [];
            }

            foreach ($fileOptionIds as $optionId) {
                if ($option = $this->buildOption($optionId, $product, $request)) {
                    $options[] = $option;
                }
            }
        }

        return $options;
    }

    /**
     * Get file options from request
     *
     * @param DataObject $request
     * @return array
     */
    private function getFileOptionIds(DataObject $request): array
    {
        $fileOptionIds = [];
        foreach ($request->getData() as $key => $value) {
            if (preg_match('/options_\d+_file_action/', $key)) {
                $fileOptionIds[] = (int) preg_replace("/[^0-9]/", '', $key);
            }
        }

        return $fileOptionIds;
    }

    /**
     * Build option
     *
     * @param int $optionId
     * @param ProductInterface $product
     * @param DataObject $request
     * @return CustomOptionInterface|null
     */
    private function buildOption(int $optionId, ProductInterface $product, DataObject $request): ?CustomOptionInterface
    {
        $option = null;
        $fileAction = 'options_' . $optionId . '_file_action';
        if ($request->getData($fileAction) == 'save_old') {
            $fileDataObject = $this->loadStoredFileData($optionId, $request);
        } else {
            $fileDataObject = $this->buildFileData($optionId, $product, $request);
        }

        if ($fileDataObject instanceof CustomOptionFileInterface) {
            $option = $this->customOptionFactory->create();
            $option
                ->setOptionId($optionId)
                ->setIsFile(true)
                ->getExtensionAttributes()->setFileData($fileDataObject);
        }

        return $option;
    }

    /**
     * Load stored file data, if file didn`t updated
     *
     * @param int $optionId
     * @param DataObject $request
     * @return CustomOptionInterface|null
     */
    private function loadStoredFileData(int $optionId, DataObject $request): ?CustomOptionFileInterface
    {
        $fileDataObject = null;
        $itemKey = $request->getData(ProductListItemInterface::ITEM_KEY);
        try {
            $item = $this->productListItemRepository->getByKey($itemKey);
            if ($item->getProductOption()
                && $item->getProductOption()->getExtensionAttributes()
                && $item->getProductOption()->getExtensionAttributes()->getAwQoCustomOptions()
            ) {
                $customOptions
                    = $item->getProductOption()->getExtensionAttributes()->getAwQoCustomOptions();
                foreach ($customOptions as $customOption) {
                    if ($customOption->getOptionId() == $optionId) {
                        $fileDataObject = $customOption->getExtensionAttributes()->getFileData();
                        break;
                    }
                }
            }
        } catch (NoSuchEntityException $e) {
        }

        return $fileDataObject;
    }

    /**
     * Build Uploaded File Data
     *
     * @param int $optionId
     * @param ProductInterface $product
     * @param DataObject $request
     * @return CustomOptionFileInterface|null
     * @throws Exception
     * @throws InputException
     * @throws LocalizedException
     * @throws \Magento\Framework\Validator\Exception
     * @throws \Zend_File_Transfer_Exception
     */
    private function buildFileData(
        int $optionId, ProductInterface $product, DataObject $request
    ): ?CustomOptionFileInterface {
        $fileDataObject = null;
        if ($productOption = $this->findOption($product->getOptions(), $optionId)) {
            $params = $this->getProcessingParams($request);
            if (!$productOption->getIsRequire()) {
                try {
                    $fileData = $this->validatorFile
                        ->setProduct($product)
                        ->validate($params, $productOption);
                } catch (\Magento\Framework\Validator\Exception $e) {
                    $fileData = [];
                }
            } else {
                $fileData = $this->validatorFile
                    ->setProduct($product)
                    ->validate($params, $productOption);
            }

            /** @var CustomOptionFileInterface $customOptionFile */
            $fileDataObject = $this->customOptionFileInterfaceFactory->create();

            $this->dataObjectHelper->populateWithArray(
                $fileDataObject,
                $fileData,
                CustomOptionFileInterface::class
            );
        }

        return $fileDataObject;
    }

    /**
     * Returns additional params for processing options
     *
     * @return DataObject
     */
    private function getProcessingParams(DataObject $request)
    {
        $params = $request->getData('_processing_params');
        if ($params instanceof \Magento\Framework\DataObject) {
            return $params;
        }

        return new \Magento\Framework\DataObject();
    }

    /**
     * Find option by option id
     *
     * @param array $options
     * @param int $optionId
     * @return ProductCustomOptionInterface|null
     */
    private function findOption(array $options, int $optionId): ?ProductCustomOptionInterface
    {
        /** @var ProductCustomOptionInterface $option */
        foreach ($options as $option) {
            if ($option->getOptionId() == $optionId) {
                return $option;
            }
        }

        return null;
    }
}
