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
namespace Aheadworks\Ctq\Model\Data\Command\Quote;

use Aheadworks\Ctq\Model\Data\CommandInterface;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;

/**
 * Class Buy
 *
 * @package Aheadworks\Ctq\Model\Data\Command\Quote
 */
class Buy implements CommandInterface
{
    /**
     * @var BuyerQuoteManagementInterface
     */
    private $buyerQuoteManagement;

    /**
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     */
    public function __construct(
        BuyerQuoteManagementInterface $buyerQuoteManagement
    ) {
        $this->buyerQuoteManagement = $buyerQuoteManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data['store_id'])) {
            throw new \InvalidArgumentException('store_id argument is required');
        }
        if (!isset($data['quote_id'])) {
            throw new \InvalidArgumentException('quote_id argument is required');
        }

        return $this->buyerQuoteManagement->buy($data['quote_id'], $data['store_id']);
    }
}
