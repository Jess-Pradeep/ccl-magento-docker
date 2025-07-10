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
namespace Aheadworks\Ctq\Api\Data;

use Magento\Quote\Api\Data\CartInterface as QuoteCartInterfaceInterface;

/**
 * Interface CartInterface
 * @api
 */
interface CartInterface extends QuoteCartInterfaceInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const AW_CTQ_AMOUNT = 'aw_ctq_amount';
    const BASE_AW_CTQ_AMOUNT = 'base_aw_ctq_amount';
    const AW_CTQ_SELLER_ID = 'aw_ctq_seller_id';
    const AW_CTQ_QUOTE_LIST_CUSTOMER_ID = 'aw_ctq_quote_list_customer_id';
    const AW_CTQ_IS_QUOTE_LIST = 'aw_ctq_is_quote_list';
    /**#@-*/
}
