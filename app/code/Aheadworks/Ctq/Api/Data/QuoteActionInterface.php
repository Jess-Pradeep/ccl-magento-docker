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
namespace Aheadworks\Ctq\Api\Data;

/**
 * Interface QuoteActionInterface
 * @api
 */
interface QuoteActionInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const TYPE = 'type';
    const NAME = 'name';
    const URL_PATH = 'url_path';
    const EXTERNAL_URL_PATH = 'external_url_path';
    const SORT_ORDER = 'sort_order';
    /**#@-*/

    /**
     * Retrieve type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieve url path
     *
     * @return string
     */
    public function getUrlPath();

    /**
     * Retrieve external url path
     *
     * @return string
     */
    public function getExternalUrlPath();

    /**
     * Retrieve sort order
     *
     * @return int
     */
    public function getSortOrder();
}
