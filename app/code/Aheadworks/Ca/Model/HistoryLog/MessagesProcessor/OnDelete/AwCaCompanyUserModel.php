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

namespace Aheadworks\Ca\Model\HistoryLog\MessagesProcessor\OnDelete;

use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Aheadworks\Ca\Model\Resolver\UserResolver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Ca\Model\HistoryLog\MessagesProcessor\ProcessorInterface;

class AwCaCompanyUserModel implements ProcessorInterface
{
    /**
     * @param UserResolver $userResolver
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly UserResolver $userResolver,
        private readonly RequestInterface $request
    ) {
    }

    /**
     * Process data after delete
     *
     * @param HistoryLogInterface $object
     * @param array $processor
     * @param AbstractModel $model
     * @return HistoryLogInterface
     */
    public function addCustomData(
        HistoryLogInterface $object,
        array $processor,
        AbstractModel $model
    ): HistoryLogInterface {
        $actionName = $this->request->getActionName();
        if ($this->userResolver->isUserAdmin()) {
            if ($actionName) {
                $object->setPerformedAction(__('%1 via Admin Panel', $processor['action'])->render());
            } else {
                $object->setPerformedAction(__('%1 By API', $processor['action'])->render());
            }
            $object->setCompanyId($model->getCompanyId());
        } else {
            $object->setPerformedAction($processor['action']);
        }
        $object->setValuesSetTo(__('Deleted Company User')->render());
        $object->setEntityId($model->getId());

        return $object;
    }
}
