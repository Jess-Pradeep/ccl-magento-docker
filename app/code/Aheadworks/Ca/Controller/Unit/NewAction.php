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

namespace Aheadworks\Ca\Controller\Unit;

use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends AbstractUnitAction
{
   /**
    * Forward to edit action
    *
    * @return Forward
    */
   public function execute(): Forward
   {
      /** @var Forward $resultForward */
      $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
      return $resultForward->forward('edit');
   }
}
