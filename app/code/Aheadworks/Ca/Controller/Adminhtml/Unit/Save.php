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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Model\Data\Command\Company\Unit\Save as UnitSaveCommand;

class Save extends Action
{
    /**
     * Save Construct
     *
     * @param Context $context
     * @param CommandInterface $saveCommand
     */
    public function __construct(
        Context $context,
        private readonly CommandInterface $saveCommand
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute(): ?ResultInterface
    {
        $unitData = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        $companyId = $unitData['current_company_id'];
        unset($unitData['current_company_id']);
        try {
            $unitId = !empty($unitData['id']) ? $unitData['id'] : false;
            $unitData['id'] = $unitId;
            $unitData[UnitSaveCommand::CURRENT_COMPANY_ID] = $companyId;
            $this->saveCommand->execute($unitData);
            $this->messageManager->addSuccessMessage(__('The unit was successfully saved.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while saving the unit.')
            );
        }

        return $resultRedirect->setPath('aw_ca/company/edit/', ['id' => $companyId]);
    }
}
