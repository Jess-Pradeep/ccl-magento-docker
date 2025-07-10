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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Model\Source\Transaction\Action as TransactionActionSource;
use Aheadworks\CreditLimit\Model\Transaction\TransactionBuilderInterface;
use Aheadworks\CreditLimit\Model\Transaction\CreditSummaryManagement;

/**
 * Class AbstractBuilder
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Builder
 */
abstract class AbstractBuilder implements TransactionBuilderInterface
{
    /**
     * @var TransactionActionSource
     */
    protected $transactionActionSource;

    /**
     * @var CreditSummaryManagement
     */
    protected $summaryManagement;

    /**
     * @param TransactionActionSource $transactionActionSource
     * @param CreditSummaryManagement $summaryManagement
     */
    public function __construct(
        TransactionActionSource $transactionActionSource,
        CreditSummaryManagement $summaryManagement
    ) {
        $this->transactionActionSource = $transactionActionSource;
        $this->summaryManagement = $summaryManagement;
    }
}
