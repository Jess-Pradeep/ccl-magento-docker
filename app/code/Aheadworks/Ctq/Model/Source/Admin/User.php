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
namespace Aheadworks\Ctq\Model\Source\Admin;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Ctq\Model\Magento\ModuleUser\UserLoader;

/**
 * Class User
 *
 * @package Aheadworks\Ctq\Model\Source\Admin
 */
class User implements OptionSourceInterface
{
    /**
     * @var UserLoader
     */
    private $userLoader;

    /**
     * @param UserLoader $userLoader
     */
    public function __construct(
        UserLoader $userLoader
    ) {
        $this->userLoader = $userLoader;
    }

    /**
     * Get option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $users = $this->userLoader->load();
        $userOptions = [];
        foreach ($users as $user) {
            $userOptions[] = [
                'value' => $user->getUserId(),
                'label' => $user->getUserFullname()
            ];
        }
        return $userOptions;
    }
}
