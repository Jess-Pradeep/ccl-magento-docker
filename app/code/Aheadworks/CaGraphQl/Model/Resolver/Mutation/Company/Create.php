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
 * @package    CaGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CaGraphQl\Model\Resolver\Mutation\Company;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Aheadworks\Ca\Controller\Company\DataProcessor;
use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RequestInterfaceFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Create implements ResolverInterface
{
    /**
     * @param RequestInterfaceFactory $requestFactory
     * @param DataProcessor $dataProcessor
     * @param SellerCompanyManagementInterface $sellerCompanyService
     */
    public function __construct(
        private readonly RequestInterfaceFactory $requestFactory,
        private readonly DataProcessor $dataProcessor,
        private readonly SellerCompanyManagementInterface $sellerCompanyService
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
     * @throws Exception
     * @return CompanyInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): CompanyInterface {
        /** @var RequestInterface $request */
        $request = $this->requestFactory->create();
        $request->setParams($args);

        $company = $this->dataProcessor->prepareCompany($request);
        $customer = $this->dataProcessor->prepareCustomer($request);
        return $this->sellerCompanyService->createCompany($company, $customer);
    }
}
