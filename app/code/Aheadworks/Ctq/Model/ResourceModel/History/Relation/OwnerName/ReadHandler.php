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
namespace Aheadworks\Ctq\Model\ResourceModel\History\Relation\OwnerName;

use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Model\ResourceModel\Owner\Relation\OwnerName\AbstractReadHandler;

/**
 * Class ReadHandler
 * @package Aheadworks\Ctq\Model\ResourceModel\History\Relation\OwnerName
 */
class ReadHandler extends AbstractReadHandler
{
    /**
     * {@inheritdoc}
     */
    protected function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(HistoryInterface::class)->getEntityConnectionName()
        );
    }
}
