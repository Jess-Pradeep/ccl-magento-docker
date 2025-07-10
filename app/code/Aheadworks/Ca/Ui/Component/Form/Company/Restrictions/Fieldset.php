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

namespace Aheadworks\Ca\Ui\Component\Form\Company\Restrictions;

use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Fieldset as FormFieldset;

/**
 * Allowed payment and shipping methods fieldset
 */
class Fieldset extends FormFieldset
{
    /**
     * @var Manager
     */
    private Manager $thirdPartyModuleManager;

    /**
     * @param ContextInterface $context
     * @param Manager $thirdPartyModuleManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Manager $thirdPartyModuleManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare(): void
    {
        parent::prepare();
        $config = $this->getData('config');
        $config['componentDisabled'] = !$this->isComponentVisible();
        $config['label'] = $this->resolveFieldsetLabel();
        $this->setData('config', $config);
    }

    /**
     * Render component in case company is modified and hide if required
     *
     * @return boolean
     */
    private function isComponentVisible(): bool
    {
        if ($this->thirdPartyModuleManager->isAwPayRestModuleEnabled()
            || $this->thirdPartyModuleManager->isAwShipRestModuleEnabled()) {
            return true;
        }

        return false;
    }

    /**
     * Resolver fieldset label
     *
     * @return string
     */
    private function resolveFieldsetLabel(): string
    {
        $label = '';
        $shipRestModuleEnabled = $this->thirdPartyModuleManager->isAwShipRestModuleEnabled();
        $payRestModuleEnabled = $this->thirdPartyModuleManager->isAwPayRestModuleEnabled();
        if ($shipRestModuleEnabled && !$payRestModuleEnabled) {
            $label = __('Allowed Shipping Methods');
        } elseif (!$shipRestModuleEnabled && $payRestModuleEnabled) {
            $label = __('Allowed Payment Methods');
        } elseif ($shipRestModuleEnabled && $payRestModuleEnabled) {
            $label = __('Allowed Payment & Shipping Methods ');
        }

        return $label instanceof Phrase ? $label->render() : $label;
    }
}
