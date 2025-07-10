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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Plugin\Customer\RequisitionList;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListPermission;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListProvider;
use Aheadworks\RequisitionLists\ViewModel\Customer\RequisitionList\DataProvider;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class DataProviderPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Plugin\Customer\RequisitionList
 */
class DataProviderPlugin implements ArgumentInterface
{
    /**
     * @var RequisitionListPermission
     */
    private $listPermission;

    /**
     * @var RequisitionListProvider
     */
    private $listProvider;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param RequisitionListPermission $listPermission
     * @param RequisitionListProvider $listProvider
     * @param SerializerInterface $serializer
     */
    public function __construct(
        RequisitionListPermission $listPermission,
        RequisitionListProvider $listProvider,
        SerializerInterface $serializer
    ) {
        $this->listPermission = $listPermission;
        $this->listProvider = $listProvider;
        $this->serializer = $serializer;
    }

    /**
     * Get JS Layout and insert needed values for modal form fields
     *
     * @param DataProvider $subject
     * @param string $result
     * @return string
     */
    public function afterGetPreparedJsLayout($subject, $result)
    {
        if ($this->listPermission->isEditable() && $this->listPermission->isCustomerHasCompanyPermissions()) {
            $jsLayout = $this->serializer->unserialize($result);

            if (isset($jsLayout['components']['awRequisitionListParent']['children']
                ['awRequisitionList']['children']['awRequisitionListForm']['children']
                ['fieldset']['children'])
            ) {
                $formFields = &$jsLayout['components']['awRequisitionListParent']['children']
                ['awRequisitionList']['children']['awRequisitionListForm']['children']
                ['fieldset']['children'];

                $formFields['shared']['visible'] = true;
                $formFields['shared']['default'] = $this->getCurrentRequisitionListIsShared();
            }

            return $this->serializer->serialize($jsLayout);
        } else {
            return $result;
        }
    }

    /**
     * Retrieve current requisition list is shared
     *
     * @return bool
     */
    private function getCurrentRequisitionListIsShared()
    {
        if ($list = $this->listProvider->getList()) {
            return $list->getShared() ? '1' : '0';
        }

        return false;
    }
}
