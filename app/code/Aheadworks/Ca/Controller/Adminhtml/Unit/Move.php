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

namespace Aheadworks\Ca\Controller\Adminhtml\Unit;

use Aheadworks\Ca\Api\UnitRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Aheadworks\Ca\Api\UnitManagementInterface;

class Move extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

    /**
     * Move Constructor.
     *
     * @param Context $context
     * @param UnitRepositoryInterface $unitRepository
     * @param JsonFactory $jsonResultFactory
     * @param UnitManagementInterface $unitManagement
     */
    public function __construct(
        Context $context,
        private readonly UnitRepositoryInterface $unitRepository,
        private readonly JsonFactory $jsonResultFactory,
        private readonly UnitManagementInterface $unitManagement
    ) {
        parent::__construct($context);
    }

    /**
     * Move Units Node
     *
     * @return void
     */
    public function execute()
    {
        /** @var Json $result */
        $resultJson = $this->jsonResultFactory->create();
        $unitsData = $this->getRequest()->getParam('nodes_data', []);

        $result = ['success' => false];
        if (!empty($unitsData)) {
            try {
                $this->unitManagement->moveUnit($unitsData);
                $result['success'] = true;
            } catch (\Exception $exception) {
                $result['message'] = $exception->getMessage();
            }
        } else {
            $result['message'] = __('No units data provided.');
        }

        return $resultJson->setData($result);
    }
}
