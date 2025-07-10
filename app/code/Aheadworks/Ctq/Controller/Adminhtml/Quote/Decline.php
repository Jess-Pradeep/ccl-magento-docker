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

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Source\Quote\Status;
use Exception;

class Decline extends Save
{
    /**
     * Prepare quote
     *
     * @param array $data
     * @return QuoteInterface
     * @throws Exception
     */
    protected function prepareQuote(array $data): QuoteInterface
    {
        $quoteObject = parent::prepareQuote($data);
        $quoteObject->setStatus(Status::DECLINED_BY_SELLER);

        return $quoteObject;
    }
}
