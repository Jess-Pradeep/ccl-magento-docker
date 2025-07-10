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
namespace Aheadworks\Ca\Controller\Adminhtml\Company\Domain;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\Ca\Model\Data\CommandInterface;
use Aheadworks\Ca\Model\Source\Company\Domain\AdminType;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;

/**
 * Class Save
 *
 * @package Aheadworks\Ca\Controller\Adminhtml\Company\Domain
 */
class Save extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ca::companies';

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
     * @inheritdoc
     */
    public function execute()
    {
        /** @var ResultJson $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $params = $this->getRequest()->getParams();

        try {
            $params[CompanyDomainInterface::REQUESTED_BY] = AdminType::BACKEND_ADMIN;
            $this->saveCommand->execute($params);
            $result = [
                'error'     => false,
                'message'   => __('Success')
            ];
        } catch (\Exception $e) {
            $result = [
                'error'     => true,
                'message'   => __($e->getMessage())
            ];
        }

        return $resultJson->setData($result);
    }
}
