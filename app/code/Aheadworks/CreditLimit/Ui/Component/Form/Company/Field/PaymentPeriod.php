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
declare(strict_types=1);

namespace Aheadworks\CreditLimit\Ui\Component\Form\Company\Field;

use Magento\Ui\Component\Form\Field;

/**
 * Class PaymentPeriod
 */
class PaymentPeriod extends Field
{
    /**
     * Prepare component configuration - disable payment period editing
     *
     * @return void
     */
    public function prepare(): void
    {
        $data = $this->getContext()->getDataProvider()->getData();
        if ($data) {
            $dueDate = $data[array_key_first($data)]['aw_credit_limit']['due_date'] ?? false;
            $this->_data['config']['disabled'] = (bool)$dueDate;
        }
        parent::prepare();
    }
}
