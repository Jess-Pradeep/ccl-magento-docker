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
namespace Aheadworks\Ctq\Plugin\Checkout\Model;

use Magento\Checkout\Model\TotalsInformationManagement;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\ResourceModel\Quote;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class TotalsInformationManagementPlugin
 */
class TotalsInformationManagementPlugin
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var Quote
     */
    private $quoteResourceModel;

    /**
     * TotalsInformationManagementPlugin constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param Quote $quoteResourceModel
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        Quote $quoteResourceModel
    ) {
        $this->cartRepository = $cartRepository;
        $this->quoteResourceModel = $quoteResourceModel;
    }

    /**
     * Save quote afrer calculate totals quote
     *
     * @param TotalsInformationManagement $subject
     * @param callable $proceed
     * @param int $cartId
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     * @return TotalsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundCalculate (
        TotalsInformationManagement $subject,
        callable $proceed,
        int $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        $total = $proceed($cartId, $addressInformation);
        $quote = $this->cartRepository->get($cartId);
        $this->quoteResourceModel->save($quote);

        return $total;
    }
}