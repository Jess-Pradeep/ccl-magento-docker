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
namespace Aheadworks\Ca\Block\Adminhtml\Page\Menu;

use Magento\Backend\Block\Template;
use Aheadworks\Ca\Block\Adminhtml\Page\Menu;

/**
 * Class Item
 *
 * @method string getPath()
 * @method string getLabel()
 * @method string getResource()
 * @method string[] getControllers()
 * @method array getLinkAttributes()
 * @method Item setLinkAttributes(array $linkAttributes)
 *
 * @package Aheadworks\Ca\Block\Adminhtml\Page\Menu
 */
class Item extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Ca::page/menu/item.phtml';

    /**
     * Prepare html attributes of the link
     *
     * @return void
     */
    protected function prepareLinkAttributes()
    {
        $linkAttributes = is_array($this->getLinkAttributes()) ? $this->getLinkAttributes() : [];
        if (!isset($linkAttributes['href'])) {
            $linkAttributes['href'] = $this->getUrl($this->getPath());
        }
        $classes = [];
        if (isset($linkAttributes['class'])) {
            $classes = explode(' ', (string)$linkAttributes['class']);
        }
        if ($this->isCurrent()) {
            $classes[] = 'current';
        }
        $linkAttributes['class'] = implode(' ', $classes);
        $this->setLinkAttributes($linkAttributes);
    }

    /**
     * Retrieves string presentation of link attributes
     *
     * @return string
     */
    public function serializeLinkAttributes()
    {
        $nameValuePairs = [];
        foreach ($this->getLinkAttributes() as $attrName => $attrValue) {
            $nameValuePairs[] = sprintf('%s="%s"', $attrName, $attrValue);
        }
        return implode(' ', $nameValuePairs);
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return $this
     */
    protected function _prepareLayout(): self
    {
        parent::_prepareLayout();
        $this->prepareLinkAttributes();
        if ($this->isCurrent()) {
            /** @var Menu $menu */
            $menu = $this->getParentBlock();
            $menu?->setTitle($this->getLabel());
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if ($this->getResource() && !$this->_authorization->isAllowed($this->getResource())) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Checks whether the item is current
     *
     * @return bool
     */
    private function isCurrent()
    {
        $isCurrent = false;
        $currentControllerName = $this->getRequest()->getControllerName();
        $controllers = $this->getControllers();
        if (isset($controllers) && is_array($controllers)) {
            $isCurrent = in_array($currentControllerName, $controllers);
        }
        return $isCurrent;
    }
}
