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
 * @package    Ca
 * @version    1.12.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Test\Unit\Model\Company;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainInterfaceFactory;
use Aheadworks\Ca\Api\Data\CompanyDomainSearchResultsInterface;
use Aheadworks\Ca\Api\Data\CompanyDomainSearchResultsInterfaceFactory;
use Aheadworks\Ca\Model\Company\Domain;
use Aheadworks\Ca\Model\Company\DomainRepository;
use Aheadworks\Ca\Model\ResourceModel\Company\Domain as CompanyDomainResourceModel;
use Aheadworks\Ca\Model\ResourceModel\Company\Domain\Collection as CompanyDomainCollection;
use Aheadworks\Ca\Model\ResourceModel\Company\Domain\CollectionFactory as CompanyDomainCollectionFactory;

/**
 * Class DomainRepositoryTest
 *
 * @package Aheadworks\Ca\Test\Unit\Model\Company
 */
class DomainRepositoryTest extends TestCase
{
    /**
     * @var DomainRepository
     */
    private $domainRepository;

    /**
     * @var CompanyDomainResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceModelMock;

    /**
     * @var CompanyDomainInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $domainFactoryMock;

    /**
     * @var CompanyDomainCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $domainCollectionFactoryMock;

    /**
     * @var CompanyDomainSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var CollectionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;
    
    /**
     * @var array
     */
    private $domainData = [
        CompanyDomainInterface::ID => 1,
        CompanyDomainInterface::NAME => 'test.com'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->resourceModelMock = $this->createPartialMock(
            CompanyDomainResourceModel::class,
            ['save', 'load', 'delete', 'setArgumentsForEntity']
        );
        $this->domainFactoryMock = $this->createPartialMock(
            CompanyDomainInterfaceFactory::class,
            ['create']
        );
        $this->domainCollectionFactoryMock = $this->createPartialMock(
            CompanyDomainCollectionFactory::class,
            ['create']
        );
        $this->searchResultsFactoryMock = $this->createPartialMock(
            CompanyDomainSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->createPartialMock(
            JoinProcessorInterface::class,
            ['process', 'extractExtensionAttributes']
        );
        $this->collectionProcessorMock = $this->getMockForAbstractClass(
            CollectionProcessorInterface::class
        );
        $this->dataObjectHelperMock = $this->createPartialMock(
            DataObjectHelper::class,
            ['populateWithArray']
        );
       
        $this->domainRepository = $objectManager->getObject(
            DomainRepository::class,
            [
                'resource' => $this->resourceModelMock,
                'companyDomainFactory' => $this->domainFactoryMock,
                'companyDomainCollectionFactory' => $this->domainCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock
            ]
        );
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $domainId = 1;

        /** @var CompanyDomainInterface|\PHPUnit_Framework_MockObject_MockObject $domainMock */
        $domainMock = $this->createMock(Domain::class);
        $this->domainFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($domainMock);
        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($domainMock, $domainId)
            ->willReturnSelf();
        $domainMock->expects($this->once())
            ->method('getId')
            ->willReturn($domainId);

        $this->assertSame($domainMock, $this->domainRepository->get($domainId));
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var CompanyDomainInterface|\PHPUnit_Framework_MockObject_MockObject $domainMock */
        $domainMock = $this->createPartialMock(Domain::class, ['getId']);
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $domainMock->expects($this->any())
            ->method('getId')
            ->willReturn($this->domainData[CompanyDomainInterface::ID]);

        $this->assertSame($domainMock, $this->domainRepository->save($domainMock));
    }

    /**
     * Testing of save method on exception
     *
     * @expectedException CouldNotSaveException
     * @expectedExceptionMessage Exception message.
     */
    public function testSaveOnException()
    {
        $exception = new CouldNotSaveException(__('Exception message.'));

        /** @var CompanyDomainInterface|\PHPUnit_Framework_MockObject_MockObject $domainMock */
        $domainMock = $this->createMock(Domain::class);
        $this->resourceModelMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);
        $this->expectException(CouldNotSaveException::class);
        $this->domainRepository->save($domainMock);
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 20
     */
    public function testGetOnException()
    {
        $domainId = 20;
        $domainMock = $this->createMock(Domain::class);
        $this->domainFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($domainMock);

        $this->resourceModelMock->expects($this->once())
            ->method('load')
            ->with($domainMock, $domainId)
            ->willReturn(null);
        $this->expectException(NoSuchEntityException::class);
        $this->domainRepository->get($domainId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var CompanyDomainCollection|\PHPUnit_Framework_MockObject_MockObject $companyCollectionMock */
        $companyCollectionMock = $this->createPartialMock(
            CompanyDomainCollection::class,
            ['getSize', 'getItems']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(CompanyDomainSearchResultsInterface::class);
        /** @var Domain|\PHPUnit_Framework_MockObject_MockObject $domainModelMock */
        $domainModelMock = $this->createPartialMock(Domain::class, ['getData']);
        /** @var CompanyDomainInterface|\PHPUnit_Framework_MockObject_MockObject $domainMock */
        $domainMock = $this->getMockForAbstractClass(CompanyDomainInterface::class);

        $this->domainCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($companyCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($companyCollectionMock, CompanyDomainInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $companyCollectionMock);
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $companyCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $companyCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$domainModelMock]);

        $domainModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->domainData);
        $this->domainFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($domainMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($domainMock, $this->domainData, CompanyDomainInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$domainMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->domainRepository->getList($searchCriteriaMock));
    }
}
