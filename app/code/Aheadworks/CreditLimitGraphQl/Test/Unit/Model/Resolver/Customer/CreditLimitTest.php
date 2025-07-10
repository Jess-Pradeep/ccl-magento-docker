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

namespace Aheadworks\CreditLimitGraphQl\Test\Unit\Model\Resolver\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\CreditLimitGraphQl\Model\Resolver\Customer\CreditLimit;
use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Model\Customer\CreditLimit\DataProvider;
use Aheadworks\CreditLimit\Model\Currency\Manager as CurrencyManager;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;

/**
 * Class CreditLimitTest unit test
 */
class CreditLimitTest extends TestCase
{
    /**
     * @var CreditLimit
     */
    private object $resolver;

    /**
     * @var CustomerManagementInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $customerServiceMock;

    /**
     * @var DataProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dataProviderMock;

    /**
     * @var CurrencyManager|\PHPUnit\Framework\MockObject\MockObject
     */
    private $currencyManagerMock;

    /**
     * @var SummaryRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $summaryRepositoryMock;

    /**
     * @var array
     */
    private array $valueMock = [];

    /**
     * @var CustomerInterface|MockObject
     */
    private $customerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->customerServiceMock = $this->getMockBuilder(CustomerManagementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataProviderMock = $this->getMockBuilder(DataProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->currencyManagerMock = $this->getMockBuilder(CurrencyManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->summaryRepositoryMock = $this->getMockBuilder(SummaryRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerMock = $this->getMockBuilder(CustomerInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMockForAbstractClass();

        $this->resolver = $objectManager->getObject(
            CreditLimit::class,
            [
                'customerService' => $this->customerServiceMock,
                'dataProvider' => $this->dataProviderMock,
                'currencyManager' => $this->currencyManagerMock,
                'summaryRepository' => $this->summaryRepositoryMock,
            ]
        );
    }

    /**
     * Test resolve method
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function testResolve()
    {
        $output = [];

        $fieldMock = $this->createMock(Field::class);
        $contextMock = $this->createMock(ContextInterface::class);
        $resolveInfoMock = $this->createMock(ResolveInfo::class);
        $this->valueMock = ['model' => $this->customerMock];

        $customerId = 0;
        $creditLimitData = ['aw_credit_limit' => []];

        $this->dataProviderMock->expects($this->once())
            ->method('getData')
            ->with($customerId)
            ->willReturn($creditLimitData);

        $this->customerServiceMock->expects($this->once())
            ->method('isCreditLimitAvailable')
            ->with($customerId)
            ->willReturn(false);

        $this->assertEquals($output, $this->resolver->resolve(
            $fieldMock,
            $contextMock,
            $resolveInfoMock,
            $this->valueMock
        ));
    }
}
