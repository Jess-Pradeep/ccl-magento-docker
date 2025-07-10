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
namespace Aheadworks\Ctq\Model\History\Notifier\VariableProcessor;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Email\VariableProcessorInterface;
use Aheadworks\Ctq\Model\Source\History\EmailVariables;
use Aheadworks\Ctq\ViewModel\Customer\Quote;

/**
 * Class ExpirationDate
 * @package Aheadworks\Ctq\Model\History\Notifier\VariableProcessor
 */
class ExpirationDate implements VariableProcessorInterface
{
    /**
     * @var Quote
     */
    private $quoteViewModel;

    /**
     * @param Quote $quoteViewModel
     */
    public function __construct(Quote $quoteViewModel)
    {
        $this->quoteViewModel = $quoteViewModel;
    }

    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        /** @var QuoteInterface $quote */
        $quote = $variables[EmailVariables::QUOTE];
        $expDate = $quote->getExpirationDate();
        if ($expDate) {
            $variables[EmailVariables::EXPIRATION_DATE] = $this->quoteViewModel->getExpiredDateFormatted($expDate);
        }

        return $variables;
    }
}
