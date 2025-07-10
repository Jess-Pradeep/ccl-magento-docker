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

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\QuickOrder\Api\Data\OperationResultInterface;
use Aheadworks\QuickOrder\Model\ProductList\OperationManager;
use Magento\Framework\App\RequestInterface;
use Aheadworks\QuickOrder\Model\Request\Validator\ItemId as RequestValidator;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;

/**
 * Class AbstractAction
 *
 * @package Aheadworks\QuickOrder\Controller\QuickOrder\Item
 */
abstract class AbstractAction extends Action
{
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var OperationManager
     */
    protected $operationManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RequestValidator
     */
    protected $requestValidator;

    /**
     * @param Context $context
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param OperationManager $operationManager
     * @param RequestValidator $requestValidator
     */
    public function __construct(
        Context $context,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        OperationManager $operationManager,
        RequestValidator $requestValidator
    ) {
        parent::__construct($context);
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->operationManager = $operationManager;
        $this->requestValidator = $requestValidator;
    }

    /**
     * Validate request params
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->requestValidator->isValid($request)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            $messages = $this->requestValidator->getMessages();
            $message = array_shift($messages);
            $result = [
                'error' => $message,
            ];
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

            return $resultJson->setData($result);
        }

        return parent::dispatch($request);
    }

    /**
     * Convert to result array
     *
     * @param OperationResultInterface $operationResult
     * @return array
     */
    protected function convertToResultArray($operationResult)
    {
        return $this->dataObjectProcessor->buildOutputDataArray(
            $operationResult,
            OperationResultInterface::class
        );
    }
}
