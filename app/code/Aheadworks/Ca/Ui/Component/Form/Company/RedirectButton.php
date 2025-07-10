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

use Magento\Ui\Component\Container;

/**
 * Class RedirectButton
 */
class RedirectButton extends Container
{
    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare(): void
    {
        $config = $this->getData('config');
        $requestParam = $this->context->getRequestParam($config['requestParamName']);
        $config['urlToRedirect'] = $this->context->getUrl(
            $config['pathToRedirect'],
            [$config['paramName'] => $requestParam]
        );
        $this->setData('config', $config);

        parent::prepare();
    }
}
