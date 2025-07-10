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
declare(strict_types=1);

namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Backend\Block\Widget\Button;

class CustomerSelection extends AbstractEdit
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->setId('aw_ctq_quote_edit_customer_selection');
    }

    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText(): Phrase
    {
        return __('Please select a customer');
    }

    /**
     * Get buttons html
     *
     * @return string
     * @throws LocalizedException
     */
    public function getButtonsHtml(): string
    {
        if ($this->_authorization->isAllowed('Magento_Customer::manage')) {
            $addButtonData = [
                'label' => __('Create New Customer'),
                'onclick' => 'quote.setCustomerId(false)',
                'class' => 'primary',
            ];
            return $this->getLayout()->createBlock(Button::class)
                ->setData($addButtonData)
                ->toHtml();
        }
        return '';
    }
}
