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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Test\Unit\Model;

use Aheadworks\CreditLimit\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Unit test for Config
 *
 * @package Aheadworks\CreditLimit\Test\Unit\Model
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $model;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * Init mocks for tests
     *
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->model = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * Test isAllowedToUpdateCreditBalance method
     */
    public function testIsAllowedToUpdateCreditBalance()
    {
        $expected = false;
        $websiteId = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                Config::XML_PATH_GENERAL_IS_ALLOWED_TO_UPDATE_CREDIT_BALANCE,
                ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            )->willReturn($expected);

        $this->assertEquals($expected, $this->model->isAllowedToUpdateCreditBalance($websiteId));
    }

    /**
     * Test getEmailSender method
     */
    public function testGetEmailSender()
    {
        $storeId = 1;
        $expected = 'general';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getEmailSender($storeId));
    }

    /**
     * Testing of getSenderName method
     */
    public function testGetSenderName()
    {
        $storeId = 1;
        $sender = 'email_sender';
        $expectedValue = 'email_sender_name';

        $this->scopeConfigMock->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnCallback(function ($value) use ($expectedValue, $sender) {
                static $invocation = 0;
                $invocation++;
                match ($invocation) {
                    1 => $this->assertEquals(Config::XML_PATH_EMAIL_SENDER, $value),
                    2 => $this->assertEquals('trans_email/ident_' . $sender . '/name', $value),
                };
                return match ($invocation) {
                    1 => $sender,
                    2 => $expectedValue
                };
            });

        $this->assertEquals($expectedValue, $this->model->getSenderName($storeId));
    }

    /**
     * Testing of getSenderEmail method
     */
    public function testGetSenderEmail()
    {
        $storeId = 1;
        $sender = 'email_sender';
        $expectedValue = 'email_sender_email';
        $this->scopeConfigMock->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnCallback(function ($value) use ($expectedValue, $sender) {
                static $invocation = 0;
                $invocation++;
                match ($invocation) {
                    1 => $this->assertEquals(Config::XML_PATH_EMAIL_SENDER, $value),
                    2 => $this->assertEquals('trans_email/ident_' . $sender . '/email', $value),
                };
                return match ($invocation) {
                    1 => $sender,
                    2 => $expectedValue
                };
            });

        $this->assertEquals($expectedValue, $this->model->getSenderEmail($storeId));
    }

    /**
     * Test isAllowedToSendEmailOnBalanceUpdate method
     */
    public function testIsAllowedToSendEmailOnBalanceUpdate()
    {
        $expected = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_CAN_SEND_EMAIL_ON_BALANCE_UPDATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isAllowedToSendEmailOnBalanceUpdate());
    }

    /**
     * Test getCreditBalanceUpdatedTemplate method
     */
    public function testGetCreditBalanceUpdatedTemplate()
    {
        $expected = 'some_email_template_id';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_CREDIT_BALANCE_UPDATED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getCreditBalanceUpdatedTemplate());
    }
}
