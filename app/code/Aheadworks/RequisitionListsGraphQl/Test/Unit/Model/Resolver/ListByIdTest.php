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
 * @package    RequisitionListsGraphQl
 * @version    1.0.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RequisitionListsGraphQl\Test\Unit\Model\Resolver;

use Aheadworks\RequisitionLists\Model\RequisitionListRepository;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionListsGraphQl\Model\Resolver\ListById;
use Exception;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;

class ListByIdTest extends TestCase
{
    /**
     * @var ListById
     */
    private $model;

    /**
     * @var RequisitionListRepositoryMock|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reqListRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->reqListRepositoryMock = $this->getMockBuilder(RequisitionListRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'get'
                ]
            )
            ->getMock();
        $this->model = $objectManager->getObject(
            ListById::class,
            [
                'requisitionListRepository' => $this->reqListRepositoryMock
            ]
        );
    }

    /**
     * Test resolve method
     *
     * @dataProvider testResolveDataProvider
     * @param int $listId
     * @param int $userId
     * @throws Exception
     */
    public function testResolve($listId, $userId)
    {
        $fieldMock = $this->createMock(Field::class);
        $listMock = $this->createMock(RequisitionListInterface::class);
        $resolveInfoMock = $this->createMock(ResolveInfo::class);
        $contextMock = $this->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(
                [
                    'getUserId'
                ]
            )
            ->getMock();

        $this->reqListRepositoryMock->expects($this->once())
            ->method('get')
            ->with($listId)
            ->willReturn($listMock);

        $contextMock->expects($this->any())
            ->method('getUserId')
            ->willReturn($userId);

        $listMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($userId);

        $this->assertEquals(
            $listMock,
            $this->model->resolve(
                $fieldMock,
                $contextMock,
                $resolveInfoMock,
                [],
                ['list_id' => 1]
            )
        );
    }

    /**
     * Data provider for testResolve method
     */
    public static function testResolveDataProvider(): array
    {
        return [
            [1, 1]
        ];
    }
}
