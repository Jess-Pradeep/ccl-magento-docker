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
namespace Aheadworks\Ca\Block\Adminhtml\Order\View;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\AuthorizationInterface;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Item;
use Magento\Backend\Block\Widget\Button\Toolbar as ButtonToolbarWidget;
use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
use Aheadworks\Ca\Model\Source\Role\OrderApproval\OrderStatus;
use Aheadworks\Ca\Model\Role\OrderApproval\IsActiveChecker;
use Aheadworks\Ca\Model\Url as UrlModel;

/**
 * Class ButtonToolbar
 *
 * @package Aheadworks\Ca\Block\Adminhtml\Order\View
 */
class ButtonToolbar extends ButtonToolbarWidget
{
    /**
     * @var IsActiveChecker
     */
    private $isActiveChecker;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * @var array
     */
    private $restrictions;

    /**
     * @param IsActiveChecker $isActiveChecker
     * @param AuthorizationInterface $authorization
     * @param UrlModel $urlModel
     * @param array $restrictions
     */
    public function __construct(
        IsActiveChecker $isActiveChecker,
        AuthorizationInterface $authorization,
        UrlModel $urlModel,
        array $restrictions
    ) {
        $this->isActiveChecker = $isActiveChecker;
        $this->authorization = $authorization;
        $this->urlModel = $urlModel;
        $this->restrictions = $restrictions;
    }

    /**
     * Manage order button actions
     *
     * @param AbstractBlock|OrderView $block
     * @param ButtonList $buttonList
     */
    public function pushButtons(AbstractBlock $block, ButtonList $buttonList)
    {
        $order = $block->getOrder();

        if ($this->isActiveChecker->isOrderUnderApprovalConsideration($order)) {
            $this->hideNativeButtons(OrderStatus::PENDING_APPROVAL, $buttonList);
            $this->addOrderApprovalButtons($block, $buttonList);
        }

        if ($this->isActiveChecker->isOrderRejected($order)) {
            $this->hideNativeButtons(OrderStatus::REJECTED, $buttonList);
        }

        parent::pushButtons($block, $buttonList);
    }

    /**
     * Hide buttons on order approval pending
     *
     * @param string $restriction
     * @param ButtonList $buttonList
     */
    private function hideNativeButtons($restriction, ButtonList $buttonList)
    {
        $buttonsToShow = $this->restrictions[$restriction]['buttons_to_show'];
        foreach ($buttonList->getItems() as $buttons) {
            /** @var Item $item */
            foreach ($buttons as $item) {
                if (!in_array($item->getId(), $buttonsToShow)) {
                    $item->isDeleted(true);
                }
            }
        }
    }

    /**
     * Add order approval buttons
     *
     * @param AbstractBlock|OrderView $block
     * @param ButtonList $buttonList
     */
    private function addOrderApprovalButtons(AbstractBlock $block, ButtonList $buttonList)
    {
        $order = $block->getOrder();
        if ($this->authorization->isAllowed('Aheadworks_Ca::order_approve')) {
            $buttonList->add(
                'aw_ca_approve',
                [
                    'label' => __('Approve'),
                    'id' => 'aw-ca-order-view-approve-button',
                    'onclick' => 'setLocation(\'' . $this->urlModel->getOrderApproveUrl($order->getId()) . '\')'
                ]
            );
        }
        if ($this->authorization->isAllowed('Aheadworks_Ca::order_reject')) {
            $buttonList->add(
                'aw_ca_reject',
                [
                    'label' => __('Reject'),
                    'id' => 'aw-ca-order-view-reject-button',
                    'onclick' => 'setLocation(\'' . $this->urlModel->getOrderRejectUrl($order->getId()) . '\')'
                ]
            );
        }
    }
}
