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
namespace Aheadworks\Ca\ViewModel\Order\View\Button;

/**
 * Class Reject
 *
 * @package Aheadworks\Ca\ViewModel\Order\View\Button
 */
class Reject extends AbstractButton
{
    /**
     * @inheritdoc
     */
    public function getSubmitUrl()
    {
        return $this->url->getOrderRejectUrl($this->getOrder()->getEntityId());
    }

    /**
     * @inheritdoc
     */
    public function getButtonTitle()
    {
        return __('Reject');
    }
}
