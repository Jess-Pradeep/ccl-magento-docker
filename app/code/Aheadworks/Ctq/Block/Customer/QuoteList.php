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
namespace Aheadworks\Ctq\Block\Customer;

use Magento\Theme\Block\Html\Pager;

/**
 * Class Quotes
 * @package Aheadworks\Ctq\Block\Customer
 * @method \Aheadworks\Ctq\ViewModel\Customer\QuoteList getQuoteListViewModel()
 * @method \Aheadworks\Ctq\ViewModel\Customer\Quote getQuoteViewModel()
 */
class QuoteList extends Quote
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getQuoteListViewModel()->getQuoteList()) {
            /** @var Pager $pager */
            $pager = $this->getLayout()
                ->createBlock(
                    Pager::class,
                    'aw_ctq.customer.quote.list.pager'
                );
            $pager->setCollection($this->getQuoteListViewModel()->getQuoteList());
            $this->setChild('pager', $pager);
            $this->getQuoteListViewModel()->getQuoteList()->load();
        }
        return $this;
    }
}
