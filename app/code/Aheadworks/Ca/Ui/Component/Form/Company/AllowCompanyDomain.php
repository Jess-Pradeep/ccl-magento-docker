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
namespace Aheadworks\Ca\Ui\Component\Form\Company;

use Magento\Ui\Component\Form\Fieldset;
use Aheadworks\Ca\Api\Data\CompanyDomainInterface;

/**
 * Class AllowCompanyDomain
 *
 * @package Aheadworks\Ca\Ui\Component\Form\Company
 */
class AllowCompanyDomain extends Fieldset
{
    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        $config['componentDisabled'] = !$this->isComponentVisible();
        $this->setData('config', $config);
    }

    /**
     * Render component in case company is modified and hide for new company
     *
     * @return boolean
     */
    private function isComponentVisible()
    {
        $domainId = $this->context->getRequestParam(CompanyDomainInterface::ID);
        return (bool)$domainId;
    }
}
