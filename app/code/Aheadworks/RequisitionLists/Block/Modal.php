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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RequisitionLists\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Modal
 */
class Modal extends Template
{
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        $result = false;
        $viewModel = $this->getListViewModel();
        $names = $this->getConfigNames();

        if ($names) {
            foreach ($names as $name) {
                $result = $viewModel->getIsEnabledByName($name);

                if (!$result) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return parent::toHtml();
    }
}