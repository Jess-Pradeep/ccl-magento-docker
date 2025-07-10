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
 * @package    CtqGraphQl
 * @version    1.0.1
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\CtqGraphQl\Test\Unit\Model;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\CtqGraphQl\Model\DataProcessor\Pool as ProcessorsPool;
use Aheadworks\CtqGraphQl\Model\ObjectConverter;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class ObjectConverterTest extends TestCase
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessorMock;

    /**
     * @var ProcessorsPool
     */
    private $processorsPoolMock;

    /**
     * @var ObjectConverter
     */
    private $converter;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->dataObjectProcessorMock = $this->createMock(DataObjectProcessor::class);
        $this->processorsPoolMock = $this->createMock(ProcessorsPool::class);

        $this->converter = $objectManager->getObject(
            ObjectConverter::class,
            [
                'processorsPool' => $this->processorsPoolMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock
            ]
        );
    }

    /**
     * Test convertToArray method
     */
    public function testConvertToArray()
    {
        $quoteData = [
            'id' => 5,
            'cart_id' => 10,
            'name' => 'My Quote',
            'store_id' => 1,
            'order_id' => null,
        ];
        $instanceName = QuoteInterface::class;
        $objectMock = $this->createConfiguredMock(QuoteInterface::class, [
            'getId' => 5,
            'getCartId' => 10,
            'getName' => 'My Quote',
            'getStoreId' => 1,
            'getOrderId' => null,
        ]);
        $assertData = $quoteData;
        $assertData['model'] = $objectMock;

        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($objectMock, $instanceName)
            ->willReturn($quoteData);
        $this->processorsPoolMock->expects($this->once())
            ->method('getForInstance')
            ->with($instanceName)
            ->willReturn(null);

        $this->assertEquals($assertData, $this->converter->convertToArray(
            $objectMock,
            $instanceName
        ));
    }
}
