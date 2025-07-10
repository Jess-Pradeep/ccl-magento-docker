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
namespace Aheadworks\QuickOrder\Controller\QuickOrder\Item;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\QuickOrder\Api\Data\ProductListItemInterface;
use Aheadworks\QuickOrder\Api\ProductListManagementInterface;
use Aheadworks\QuickOrder\Model\ProductList\OperationManager;
use Aheadworks\QuickOrder\Model\Request\Validator\ItemId as RequestValidator;

/**
 * Class Remove
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder\Item
 */
class Remove extends AbstractAction
{
    /**
     * @var ProductListManagementInterface
     */
    private $productListService;

    /**
     * @param Context $context
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param OperationManager $operationManager
     * @param ProductListManagementInterface $productListService
     * @param RequestValidator $requestValidator
     */
    public function __construct(
        Context $context,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        OperationManager $operationManager,
        ProductListManagementInterface $productListService,
        RequestValidator $requestValidator,
    ) {
        parent::__construct($context, $dataObjectProcessor, $storeManager, $operationManager, $requestValidator);
        $this->productListService = $productListService;
    }

    /**
     * Remove item
     *
     * @return ResultInterface
     */
    public function execute()
    {
        try {
            $itemKey = $this->getRequest()->getParam(ProductListItemInterface::ITEM_KEY);
            $operationResult = $this->productListService->removeItem($itemKey);
            $result = $this->convertToResultArray($operationResult);
        } catch (\Exception $exception) {
            $result = [
                'error' => $exception->getMessage(),
            ];
        }
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($result);
    }
}
