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

namespace Aheadworks\Ca\Block\Company;

use Magento\Framework\View\Element\Template;

/**
 * @method \Aheadworks\Ca\ViewModel\Company\NewAdminSelection\LayoutModifier getLayoutModifier()
 */
class ChangeCompanyAdminButton extends Template
{
    /**
     * Retrieve serialized JS layout configuration ready to use in template
     *
     * @return string
     */
    public function getJsLayout(): string
    {
        $layoutModifier = $this->getLayoutModifier();
        $this->jsLayout = $layoutModifier->modify($this->jsLayout);

        return parent::getJsLayout();
    }
}
