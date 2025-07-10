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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Fieldset;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Api\CompanyAdminCandidateManagementInterface;

class AllowAdminCandidate extends Fieldset
{
    /**
     * @param ContextInterface $context
     * @param CompanyAdminCandidateManagementInterface $candidateManagement
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        private readonly CompanyAdminCandidateManagementInterface $candidateManagement,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
    }

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
     * Render component in case company is modified and hide if required
     *
     * @return boolean
     */
    private function isComponentVisible(): bool
    {
        $companyId = $this->context->getRequestParam(CompanyInterface::ID);
        if (!$companyId) {
            return false;
        }

        return $this->candidateManagement->isApproveRequired((int)$companyId);
    }
}
