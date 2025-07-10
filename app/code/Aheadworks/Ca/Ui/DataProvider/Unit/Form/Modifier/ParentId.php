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

namespace Aheadworks\Ca\Ui\DataProvider\Unit\Form\Modifier;

use Aheadworks\Ca\Api\Data\UnitInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\App\RequestInterface;
use Aheadworks\Ca\Api\UnitRepositoryInterface;

class ParentId implements ModifierInterface
{
    /**
     * ParentId Constructor
     *
     * @param RequestInterface $request
     * @param UnitRepositoryInterface $unitRepository
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly UnitRepositoryInterface $unitRepository
    ) {
    }

    /**
     * Modifiy Data
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data): array
    {
        $parentId = (isset($data['parent_id'])) ? $data['parent_id'] : $this->request->getParam('parent', 0);
        if ($parentId == 0) {
            $companyId = $this->request->getParam('company_id', 0);
            $companyUnitParent = $this->unitRepository->getCompanyRootUnit($companyId);
            $parentId = $companyUnitParent->getId();
        }
        $data[UnitInterface::PARENT_ID] = $parentId;
        return $data;
    }

    /**
     * Modify Meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta): array
    {
        return $meta;
    }
}
