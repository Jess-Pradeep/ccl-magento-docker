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
namespace Aheadworks\Ctq\Model\Quote\Admin\Session;

use Magento\Backend\Model\Session\Quote as QuoteSession;
use Magento\Quote\Model\Quote as QuoteModel;

/**
 * Class Quote
 *
 * @method Quote setIsGuestQuote($isGuestQuote)
 * @method bool getIsGuestQuote()
 * @package Aheadworks\Ctq\Model\Quote\Admin\Session
 */
class Quote extends QuoteSession
{

    /**
     * @var string
     */
    private $configKey;

    /**
     * Prepare param
     *
     * @param string $param
     * @return string
     */
    private function prepareParam($param)
    {
        return $this->configKey ? $param . '_' . $this->configKey : $param;
    }

    /**
     * @inerhitDoc
     */
    public function __call($method, $args)
    {
        return parent::__call($this->prepareParam($method), $args);
    }
    /**
     * Set quote object to session
     *
     * @param QuoteModel $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Set config key
     *
     * @param string $configKey
     * @return $this
     */
    public function setConfigKey($configKey)
    {
        $this->configKey = (string)$configKey;
        return $this;
    }

    /**
     * @inerhitDoc
     */
    public function clearStorage()
    {
        if ($this->configKey) {
            foreach ($this->storage->getData() as $key => $value) {
                if (substr_compare($key, $this->configKey, -strlen($this->configKey)) === 0) {
                    $this->storage->unsetData($key);
                }
            }

            return $this;
        }
        return parent::clearStorage();
    }
}
