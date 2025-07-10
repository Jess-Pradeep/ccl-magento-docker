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
 * @package    Ctq
 * @version    1.9.2
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Controller\Quote;

use Aheadworks\Ctq\Model\Source\Quote\Status;

/**
 * Class Decline
 *
 * @package Aheadworks\Ctq\Controller\Quote
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Decline extends ChangeStatus
{
    /**
     * @inheritdoc
     */
    protected function changeStatus($status)
    {
        $status = Status::DECLINED_BY_BUYER;
        parent::changeStatus($status);
    }
}
