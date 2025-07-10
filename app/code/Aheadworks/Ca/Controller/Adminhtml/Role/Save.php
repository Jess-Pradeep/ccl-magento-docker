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

namespace Aheadworks\Ca\Controller\Adminhtml\Role;

use Aheadworks\Ca\Model\Data\Command\Role\Save as RoleSaveCommand;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Aheadworks\Ca\Model\Data\CommandInterface;

/**
 * Class Save
 */
class Save extends Action
{
    /**
     * @var CommandInterface
     */
    private $saveCommand;

    /**
     * @param Context $context
     * @param CommandInterface $saveCommand
     */
    public function __construct(
        Context $context,
        CommandInterface $saveCommand
    ) {
        parent::__construct($context);
        $this->saveCommand = $saveCommand;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute(): ?ResultInterface
    {
        $requestData = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        $companyId = $requestData['current_company_id'];
        unset($requestData['current_company_id']);
        try {
            $requestData[RoleSaveCommand::CURRENT_COMPANY_ID] = $companyId;
            $this->saveCommand->execute($requestData);
            $this->messageManager->addSuccessMessage(__('The role was successfully saved.'));

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while saving the role.')
            );
        }

        return $resultRedirect->setPath('aw_ca/company/edit/', ['id' => $companyId]);
    }
}
