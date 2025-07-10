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

namespace Aheadworks\Ca\Block;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class ButtonWithCheckAcl
 *
 * @method string getLink()
 * @method string setLink($link)
 * @method string getAdditionalClasses()
 * @method string getLabel()
 * @package Aheadworks\Ca\Block
 */
class ButtonWithCheckAcl extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Ca::button_with_check_acl.phtml';

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * ButtonWithCheckAcl constructor.
     * @param Context $context
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param array $data
     */
    public function __construct(
        Context $context,
        AuthorizationManagementInterface $authorizationManagement,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->authorizationManagement = $authorizationManagement;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml(): string
    {
        $path = $this->createPathFromLink($this->getLink());
        if (!$this->authorizationManagement->isAllowed($path)) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Escape link and create path for acl
     *
     * @param string $link
     * @return string
     */
    private function createPathFromLink(string $link): string
    {
        $path = trim(
        //phpcs:ignore Magento2.Functions.DiscouragedFunction
            parse_url($link, PHP_URL_PATH),
            '/'
        );
        $asArray = explode('/', $path);

        return implode('/', array_slice($asArray, 0, 3));
    }
}
