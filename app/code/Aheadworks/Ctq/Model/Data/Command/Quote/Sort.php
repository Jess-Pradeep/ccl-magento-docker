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
use Aheadworks\Ctq\Model\Attachment\File\Downloader as FileDownloader;

/**
 * Class Sort
 *
 * @package Aheadworks\Ctq\Model\Data\Command\Quote
 */
class Sort implements CommandInterface
{
    /**
     * @var BuyerQuoteManagementInterface
     */
    private $buyerQuoteManagement;

    /**
     * @var FileDownloader
     */
    private $fileDownloader;

    /**
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param FileDownloader $fileDownloader
     */
    public function __construct(
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        FileDownloader $fileDownloader
    ) {
        $this->buyerQuoteManagement = $buyerQuoteManagement;
        $this->fileDownloader = $fileDownloader;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data['sort'])) {
            throw new \InvalidArgumentException('sort argument is required');
        }
        if (!isset($data['quote_id'])) {
            throw new \InvalidArgumentException('quote_id argument is required');
        }

        $this->buyerQuoteManagement->changeQuoteItemsOrder($data['quote_id'], $data['sort']);
        return true;
    }
}
