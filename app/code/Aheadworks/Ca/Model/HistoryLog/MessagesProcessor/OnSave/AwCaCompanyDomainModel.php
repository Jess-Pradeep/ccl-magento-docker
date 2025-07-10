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

namespace Aheadworks\Ca\Model\HistoryLog\MessagesProcessor\OnSave;

use Aheadworks\Ca\Api\Data\HistoryLogInterface;
use Aheadworks\Ca\Model\HistoryLog\MessagesProcessor\ProcessorInterface;
use Aheadworks\Ca\Model\Resolver\UserResolver;
use Magento\Framework\Model\AbstractModel;

class AwCaCompanyDomainModel implements ProcessorInterface
{
    /**
     * @var UserResolver
     */
    private $userResolver;

    /**
     * @param UserResolver $userResolver
     */
    public function __construct(
        UserResolver $userResolver
    ) {
        $this->userResolver = $userResolver;
    }

    /**
     * Process data before save
     *
     * @param HistoryLogInterface $object
     * @param array $processor
     * @param AbstractModel $model
     * @return HistoryLogInterface
     */
    public function addCustomData(HistoryLogInterface $object, array $processor, AbstractModel $model): HistoryLogInterface
    {
        if ($this->userResolver->isUserAdmin()) {
            $object->setCompanyId($model->getCompanyId());
        }
        $object->setPerformedAction($processor['action']);
        $object->setEntityId((int)$model->getId());
        if ($model->isObjectNew()) {
            $object->setValuesSetTo(__('New Domain %1', $model->getName())->render());
        } else {
            $object->setValuesSetTo(__('Updated Domain %1', $model->getName())->render());
        }
        return $object;
    }
}
