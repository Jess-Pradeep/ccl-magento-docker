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

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Ctq\Model\Data\CommandInterface;
use Aheadworks\Ctq\Api\BuyerQuoteManagementInterface;
use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\Data\CommentInterfaceFactory;
use Aheadworks\Ctq\Api\Data\RequestQuoteInputInterface;
use Aheadworks\Ctq\Api\Data\RequestQuoteInputInterfaceFactory;

/**
 * Class Submit
 *
 * @package Aheadworks\Ctq\Model\Data\Command\Quote
 */
class Submit implements CommandInterface
{
    /**
     * @var CommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var RequestQuoteInputInterfaceFactory
     */
    private $requestInputFactory;

    /**
     * @var BuyerQuoteManagementInterface
     */
    private $buyerQuoteManagement;

    /**
     * @param BuyerQuoteManagementInterface $buyerQuoteManagement
     * @param CommentInterfaceFactory $commentFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param RequestQuoteInputInterfaceFactory $requestInputFactory
     */
    public function __construct(
        BuyerQuoteManagementInterface $buyerQuoteManagement,
        CommentInterfaceFactory $commentFactory,
        DataObjectHelper $dataObjectHelper,
        RequestQuoteInputInterfaceFactory $requestInputFactory
    ) {
        $this->commentFactory = $commentFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->requestInputFactory = $requestInputFactory;
        $this->buyerQuoteManagement = $buyerQuoteManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data['is_guest_quote'])) {
            throw new \InvalidArgumentException('is_guest_quote argument is required');
        }
        if (!isset($data['quote_id'])) {
            throw new \InvalidArgumentException('quote_id argument is required');
        }
        if (!isset($data['request'])) {
            throw new \InvalidArgumentException('request argument is required');
        }
        if (!isset($data['is_quote_list'])) {
            throw new \InvalidArgumentException('is_quote_list argument is required');
        }

        /** @var RequestQuoteInputInterface $requestInput */
        $requestInput = $this->requestInputFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $requestInput,
            $data['request']->getParams(),
            RequestQuoteInputInterface::class
        );
        $requestInput->setIsGuestQuote($data['is_guest_quote']);

        if ($data['is_quote_list']) {
            return $this->buyerQuoteManagement->requestQuoteListByRequest(
                $data['quote_id'],
                $requestInput
            );
        } else {
            return $this->buyerQuoteManagement->requestQuoteByRequest(
                $data['quote_id'],
                $requestInput
            );
        }
    }
}
