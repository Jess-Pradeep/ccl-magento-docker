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
namespace Aheadworks\Ca\Test\Unit\Model;

use Aheadworks\Ca\Model\Config;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Ca\Model\Source\Customer\RegistrationType;

/**
 * Class ConfigTest
 *
 * @package Aheadworks\Ca\Test\Unit\Model
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
     * @return void
     */
    public function setUp(): void
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
     * Test isExtensionEnabled method
     */
    public function testIsExtensionEnabled()
    {
        $expected = false;
        $websiteId = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_GENERAL_IS_ENABLED, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isExtensionEnabled($websiteId));
    }

    /**
     * Test getDefaultSalesRepresentative method
     */
    public function testGetDefaultSalesRepresentative()
    {
        $expected = '2';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_DEFAULT_SALES_REPRESENTATIVE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getDefaultSalesRepresentative());
    }

    /**
     * Test isOrderApprovalEnabled method
     */
    public function testIsOrderApprovalEnabled()
    {
        $expected = false;
        $websiteId = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_GENERAL_ENABLED_ORDER_APPROVAL, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isOrderApprovalEnabled($websiteId));
    }

    /**
     * Test isRegistrationOnFrontendEnabled method
     *
     * @dataProvider isRegistrationOnFrontendEnabledDataProvider
     * @param string $type
     * @param string $configValue
     * @param bool $result
     */
    public function testIsRegistrationOnFrontendEnabled($type, $configValue, $result)
    {
        $websiteId = 9999;
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                Config::XML_PATH_GENERAL_ENABLED_REGISTRATION_FOR,
                ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            )->willReturn($configValue);

        $this->assertEquals($result, $this->model->isRegistrationOnFrontendEnabled($type, $websiteId));
    }

    /**
     * Data provider for testIsRegistrationOnFrontendEnabled method
     */
    public function isRegistrationOnFrontendEnabledDataProvider()
    {
        return [
            [RegistrationType::COMPANY, '', false],
            [RegistrationType::COMPANY, 'customer', false],
            [RegistrationType::COMPANY, 'company', true],
            [RegistrationType::COMPANY, 'customer,company', true],
            [RegistrationType::COMPANY, null, false]
        ];
    }

    /**
     * Test isUserApprovedAutomatically method
     */
    public function testIsUserApprovedAutomatically()
    {
        $expected = false;
        $websiteId = 1;

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_GENERAL_IS_USER_APPROVED_AUTOMATICALLY, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->isUserApprovedAutomatically($websiteId));
    }

    /**
     * Test getEmailSender method
     */
    public function testGetEmailSender()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getEmailSender());
    }

    /**
     * Test getSenderName method
     */
    public function testGetSenderName()
    {
        $expected = 'test_value';
        $this->scopeConfigMock->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnCallback(function ($value) use ($expected) {
                static $invocation = 0;
                $invocation++;
                match ($invocation) {
                    1 => $this->assertEquals(Config::XML_PATH_EMAIL_SENDER, $value),
                    2 => $this->assertEquals('trans_email/ident_' . $expected . '/name', $value),
                };
                return match ($invocation) {
                    1, 2 => $expected
                };
            });

        $this->assertEquals($expected, $this->model->getSenderName());
    }

    /**
     * Test getSenderEmail method
     */
    public function testGetSenderEmail()
    {
        $expected = 'test_value';
        $this->scopeConfigMock->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnCallback(function ($value) use ($expected) {
                static $invocation = 0;
                $invocation++;
                match ($invocation) {
                    1 => $this->assertEquals(Config::XML_PATH_EMAIL_SENDER, $value),
                    2 => $this->assertEquals('trans_email/ident_' . $expected . '/email', $value),
                };
                return match ($invocation) {
                    1, 2 => $expected
                };
            });

        $this->assertEquals($expected, $this->model->getSenderEmail());
    }

    /**
     * Test getNewCompanyApprovedTemplate method
     */
    public function testGetNewCompanyApprovedTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_COMPANY_APPROVED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewCompanyApprovedTemplate());
    }

    /**
     * Test getNewCompanyApprovedTemplate method
     */
    public function testGetNewCompanySubmittedTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_COMPANY_SUBMITTED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewCompanySubmittedTemplate());
    }

    /**
     * Test getNewCompanyDeclinedTemplate method
     */
    public function testGetNewCompanyDeclinedTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_COMPANY_DECLINED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewCompanyDeclinedTemplate());
    }

    /**
     * Test getCompanyStatusChangedTemplate method
     */
    public function testGetCompanyStatusChangedTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_COMPANY_STATUS_CHANGED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getCompanyStatusChangedTemplate());
    }

    /**
     * Test getNewCompanyUserCreatedTemplate method
     */
    public function testGetNewCompanyUserCreatedTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_COMPANY_USER_CREATED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewCompanyUserCreatedTemplate());
    }

    /**
     * Test getNewPendingCompanyUserAssignedForCompanyUserTemplate method
     */
    public function testGetNewPendingCompanyUserAssignedForCompanyUserTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_PENDING_COMPANY_USER_ASSIGNED_FOR_COMPANY_USER_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewPendingCompanyUserAssignedForCompanyUserTemplate());
    }

    /**
     * Test getNewPendingCompanyUserAssignedForCompanyAdminTemplate method
     */
    public function testGetNewPendingCompanyUserAssignedForCompanyAdminTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_PENDING_COMPANY_USER_ASSIGNED_FOR_COMPANY_ADMIN_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewPendingCompanyUserAssignedForCompanyAdminTemplate());
    }

    /**
     * Test getNewCompanyUserAssignedForCompanyAdminTemplate method
     */
    public function testGetNewCompanyUserAssignedForCompanyAdminTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_COMPANY_USER_ASSIGNED_FOR_COMPANY_ADMIN_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewCompanyUserAssignedForCompanyAdminTemplate());
    }

    /**
     * Test getNewCompanyUserAssignedForCompanyUserTemplate method
     */
    public function testGetNewCompanyUserAssignedForCompanyUserTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_NEW_COMPANY_USER_ASSIGNED_FOR_COMPANY_USER_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewCompanyUserAssignedForCompanyUserTemplate());
    }

    /**
     * Test getNewCompanyDomainCreatedByCompanyAdminTemplate method
     */
    public function testGetNewCompanyDomainCreatedByCompanyAdminTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_DOMAIN_CREATED_BY_COMPANY_ADMIN_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getNewCompanyDomainCreatedByCompanyAdminTemplate());
    }

    /**
     * Test getCompanyDomainApprovedByBackendAdminTemplate method
     */
    public function testGetCompanyDomainApprovedByBackendAdminTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_DOMAIN_APPROVED_BY_BACKEND_ADMIN_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getCompanyDomainApprovedByBackendAdminTemplate());
    }

    /**
     * Test getCompanyDomainStatusChangedByBackendAdminTemplate method
     */
    public function testGetCompanyDomainStatusChangedByBackendAdminTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_DOMAIN_STATUS_CHANGED_BY_BACKEND_ADMIN_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getCompanyDomainStatusChangedByBackendAdminTemplate());
    }

    /**
     * Test getCompanyDomainStatusChangedByCompanyAdminTemplate method
     */
    public function testGetCompanyDomainStatusChangedByCompanyAdminTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_DOMAIN_STATUS_CHANGED_BY_COMPANY_ADMIN_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getCompanyDomainStatusChangedByCompanyAdminTemplate());
    }

    /**
     * Test getCompanyDomainDeletedByBackendAdminTemplate method
     */
    public function testGetCompanyDomainDeletedByBackendAdminTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_DOMAIN_DELETED_BY_BACKEND_ADMIN_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getCompanyDomainDeletedByBackendAdminTemplate());
    }

    /**
     * Test getCompanyDomainDeletedByCompanyAdminTemplate method
     */
    public function testGetCompanyDomainDeletedByCompanyAdminTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_DOMAIN_DELETED_BY_COMPANY_ADMIN_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getCompanyDomainDeletedByCompanyAdminTemplate());
    }

    /**
     * Test getOrderWasSentForApprovalTemplate method
     */
    public function testGetOrderWasSentForApprovalTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_ORDER_WAS_SENT_FOR_APPROVAL_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getOrderWasSentForApprovalTemplate());
    }

    /**
     * Test getOrderStatusChangedTemplate method
     */
    public function testGetOrderStatusChangedTemplate()
    {
        $expected = 'test_value';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_ORDER_STATUS_CHANGED_TEMPLATE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getOrderStatusChangedTemplate());
    }
}
