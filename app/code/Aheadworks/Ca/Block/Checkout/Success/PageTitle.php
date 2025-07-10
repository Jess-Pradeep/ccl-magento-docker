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
namespace Aheadworks\Ca\Block\Checkout\Success;

use Magento\Theme\Block\Html\Title as HtmlTitle;
use Aheadworks\Ca\ViewModel\Order\SuccessPage;

/**
 * Class PageTitle
 *
 * @package Aheadworks\Ca\Block\Checkout\Success
 * @method SuccessPage getSuccessPageViewModel()
 */
class PageTitle extends HtmlTitle
{
    /**
     * @inheritdoc
     */
    public function getPageHeading()
    {
        $successPageViewModel = $this->getSuccessPageViewModel();
        return $successPageViewModel->isOrderStatusCompanyPendingApproval()
            ? $successPageViewModel->getSuccessMessage()
            : parent::getPageHeading();
    }
}
