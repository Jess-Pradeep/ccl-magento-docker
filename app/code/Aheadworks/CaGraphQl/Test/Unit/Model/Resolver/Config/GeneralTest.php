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
namespace Aheadworks\CaGraphQl\Test\Unit\Model\Resolver\Config;

use Exception;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Aheadworks\Ca\Model\Config as CaConfig;
use Aheadworks\CaGraphQl\Model\Resolver\Config\General as ConfigGeneral;

class GeneralTest extends TestCase
{
    /**
     * @var ConfigGeneral
     */
    private object $resolver;

    /**
     * @var MockObject
     */
    private MockObject $caConfigMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->caConfigMock = $this->getMockBuilder(CaConfig::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'isExtensionEnabled',
                    'isOrderApprovalEnabled',
                    'isHistoryLogEnabled',
                ]
            )
            ->getMock();

        $this->resolver = $objectManager->getObject(
            ConfigGeneral::class,
            [
                'caConfig' => $this->caConfigMock,
            ]
        );
    }

    /**
     * Test resolve method
     *
     * @throws Exception
     */
    public function testResolve()
    {
        $configData = [
            'is_module_enabled' => true,
            'is_order_approval_enabled' => true,
            'is_history_log_enabled' => true
        ];

        $fieldMock = $this->createMock(Field::class);
        $contextMock = $this->createMock(ContextInterface::class);
        $resolveInfoMock = $this->createMock(ResolveInfo::class);

        $this->caConfigMock->expects($this->once())
            ->method('isExtensionEnabled')
            ->willReturn(true);
        $this->caConfigMock->expects($this->once())
            ->method('isOrderApprovalEnabled')
            ->willReturn(true);
        $this->caConfigMock->expects($this->once())
            ->method('isHistoryLogEnabled')
            ->willReturn(true);

        $this->assertEquals($configData, $this->resolver->resolve(
            $fieldMock,
            $contextMock,
            $resolveInfoMock,
            [],
            []
        ));
    }
}
