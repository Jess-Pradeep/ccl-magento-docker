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
 * @package    CreditLimitGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CreditLimitGraphQl\Model\Resolver\Customer;

use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Model\Customer\CreditLimit\DataProvider;
use Aheadworks\CreditLimit\Model\Currency\Manager as CurrencyManager;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;

/**
 * Class CreditLimit customer info
 */
class CreditLimit implements ResolverInterface
{
    /**
     * CreditLimit constructor.
     *
     * @param CustomerManagementInterface $customerService
     * @param DataProvider $dataProvider
     * @param CurrencyManager $currencyManager
     * @param SummaryRepositoryInterface $summaryRepository
     */
    public function __construct(
        private CustomerManagementInterface $customerService,
        private DataProvider $dataProvider,
        private CurrencyManager $currencyManager,
        private SummaryRepositoryInterface $summaryRepository
    ) {
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws LocalizedException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): array {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        /** @var Customer $customer */
        $customer = $value['model'];
        $customerId = (int)$customer->getId();
        $creditLimitData = $this->dataProvider->getData($customerId, $customer->getWebsiteId());
        $output = [];

        if ($this->customerService->isCreditLimitAvailable($customerId) &&
            isset($creditLimitData[DataProvider::CREDIT_LIMIT_DATA_SCOPE]['totals'])) {
            $totals = $creditLimitData[DataProvider::CREDIT_LIMIT_DATA_SCOPE]['totals'];
            $summary = $this->summaryRepository->getByCustomerId($customerId);
            $currency = $summary->getCurrency();
            $output['totals'] = [
                'credit_terms' => $totals[SummaryInterface::PAYMENT_PERIOD] ?? null,
                'due_date' => $totals[SummaryInterface::DUE_DATE] ?? null,
                'credit_balance' => $this->currencyManager->getFormattedPriceData(
                    $totals[SummaryInterface::CREDIT_BALANCE],
                    $currency
                ),
                'available_credit' => $this->currencyManager->getFormattedPriceData(
                    $totals[SummaryInterface::CREDIT_AVAILABLE],
                    $currency
                ),
                'credit_limit' => $this->currencyManager->getFormattedPriceData(
                    $totals[SummaryInterface::CREDIT_LIMIT],
                    $currency
                ),
            ];
        }

        return $output;
    }
}
