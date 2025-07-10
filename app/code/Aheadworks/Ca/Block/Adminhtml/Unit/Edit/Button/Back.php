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

namespace Aheadworks\Ca\Block\Adminhtml\Unit\Edit\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\RequestInterface;

class Back extends AbstractButton
{
    /**
     * Back Construct
     *
     * @param Context $context
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        private readonly RequestInterface $request
    ) {
        parent::__construct($context);
    }

    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData(): array
    {
        $companyId = $this->request->getParam('company_id');

        return [
            'label' => __('Back'),
            'on_click' => sprintf(
                "location.href = '%s';",
                $this->getUrl(
                    'aw_ca/company/edit/',
                    ['id' => $companyId]
                )
            ),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
