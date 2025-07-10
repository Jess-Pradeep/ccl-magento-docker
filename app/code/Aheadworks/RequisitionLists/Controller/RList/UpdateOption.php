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

namespace Aheadworks\RequisitionLists\Controller\RList;

use Aheadworks\RequisitionLists\Api\RequisitionListManagementInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListItemInterface;

class UpdateOption extends Action
{
    /**
     * @param Context $context
     * @param RequisitionListManagementInterface $requisitionListManagement
     */
    public function __construct(
        Context $context,
        private readonly RequisitionListManagementInterface $requisitionListManagement
    ) {
        parent::__construct($context);
    }

    /**
     * Configure item
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $itemId = $this->getRequest()->getParam(RequisitionListItemInterface::ITEM_ID);
        if (!$itemId) {
            $result = [
                'error' => __('Product list item ID is required'),
            ];

            return $resultJson->setData($result);
        }

        try {
            $buyRequest = $this->getRequest()->getParams();
            $this->requisitionListManagement->updateItemOption((int)$itemId, $buyRequest);
            $result = [
                'success' => true,
            ];
        } catch (\Exception $exception) {
            $result = [
                'error' => $exception->getMessage(),
            ];
        }
        return $resultJson->setData($result);
    }
}
