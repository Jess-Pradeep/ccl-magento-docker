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
declare(strict_types=1);

namespace Aheadworks\Ca\Ui\Component\Form\Company;

use Magento\Ui\Component\Form\Fieldset;
use Aheadworks\Ca\Api\Data\CompanyInterface;

class AllowCompanyUsers extends Fieldset
{
    /**
     * Prepare component configuration
     *
     * @return void
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
    private function isComponentVisible(): bool
    {
        $companyId = $this->context->getRequestParam(CompanyInterface::ID);

        return (bool)$companyId;
    }
}
