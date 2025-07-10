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
 * @package    RequisitionLists
 * @version    1.2.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RequisitionLists\Test\Unit\Model;

use Aheadworks\RequisitionLists\Model\RequisitionListRepository;
use Aheadworks\RequisitionLists\Model\ResourceModel\RequisitionList as RequisitionListResource;
use Aheadworks\RequisitionLists\Model\RequisitionList;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class RequisitionListRepositoryTest
 * @package Aheadworks\RequisitionLists\Test\Unit\Model
 */
class RequisitionListRepositoryTest extends TestCase
{
    /**
     * @var RequisitionListRepository
     */
    private $model;

    /**
     * @var RequisitionListResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var array
     */
    private $listData = [
        'list_id' => 1
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(
            RequisitionListResource::class,
            ['save']
        );
        $this->model = $objectManager->getObject(
            RequisitionListRepository::class,
            [
                'resource' => $this->resourceMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var RequisitionListInterface|\PHPUnit_Framework_MockObject_MockObject $listMock */
        $listMock = $this->createPartialMock(RequisitionList::class, ['getListId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $listMock->expects($this->once())
            ->method('getListId')
            ->willReturn($this->listData['list_id']);

        $this->assertSame($listMock, $this->model->save($listMock));
    }
}
