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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action as BackendAction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Aheadworks\Ctq\Model\Data\CommandInterface;

/**
 * Class Download
 *
 * @package Aheadworks\Ctq\Controller\Adminhtml\Quote
 */
class Download extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Ctq::quotes';

    /**
     * {@inheritdoc}
     */
    protected $_publicActions = ['download'];

    /**
     * @var CommandInterface
     */
    private $downloadCommand;

    /**
     * @param Context $context
     * @param CommandInterface $downloadCommand
     */
    public function __construct(
        Context $context,
        CommandInterface $downloadCommand
    ) {
        parent::__construct($context);
        $this->downloadCommand = $downloadCommand;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $data = [
                'file' => $this->getRequest()->getParam('file'),
                'comment_id' => $this->getRequest()->getParam('comment_id'),
                'quote_id' => $this->getRequest()->getParam('quote_id')
            ];
            return $this->downloadCommand->execute($data);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
