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

namespace Aheadworks\Ca\Model\Authorization\Acl\Loader;

use Magento\Framework\Acl\LoaderInterface;
use Magento\Framework\Acl;
use Aheadworks\Ca\Model\Source\Role\Permission\Type;
use Magento\Framework\Exception\LocalizedException;

class Rule extends Role implements LoaderInterface
{
    /**
     * Populate ACL with data from external storage
     *
     * @param Acl $acl
     * @return void
     * @throws LocalizedException
     */
    public function populateAcl(Acl $acl): void
    {
        foreach ($this->getCompanyRoles() as $role) {
            $roleId = $role->getId();

            foreach ($role->getPermissions() as $permission) {
                $resource = $permission->getResourceId();
                if ($this->isResourceAllowed($acl, $resource)) {
                    if ($permission->getPermission() == Type::ALLOW) {
                        if ($resource === $this->rootResource->getId()) {
                            $acl->allow($roleId);
                        }
                        $acl->allow($roleId, $resource);
                    } elseif ($permission->getPermission() == Type::DENY) {
                        $acl->deny($roleId, $resource);
                    }
                }
            }
        }
    }

    /**
     * Check if ACL resource is allowed
     *
     * @param Acl $acl
     * @param string $resource
     * @return bool
     */
    private function isResourceAllowed(Acl $acl, string $resource): bool
    {
        //todo remove old Zend "has" method when support only M2.4.6 and higher
        return method_exists($acl, 'has')
            ? $acl->has($resource)
            : $acl->hasResource($resource);
    }
}
