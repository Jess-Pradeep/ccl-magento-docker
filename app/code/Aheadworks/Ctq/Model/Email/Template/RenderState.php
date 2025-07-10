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
namespace Aheadworks\Ctq\Model\Email\Template;

/**
 * Class RenderState
 *
 * @package Aheadworks\Ctq\Model\Email\Template
 */
class RenderState
{
    /**
     * @var bool
     */
    private $isRendering = false;

    /**
     * Is rendering state flag
     *
     * @param bool|null $isRendering
     * @return bool|null
     */
    public function isRendering($isRendering = null)
    {
        if ($isRendering !== null) {
            $this->isRendering = $isRendering;
        }

        return $this->isRendering;
    }
}
