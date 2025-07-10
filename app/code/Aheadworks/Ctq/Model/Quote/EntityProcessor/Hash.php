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
namespace Aheadworks\Ctq\Model\Quote\EntityProcessor;

use Aheadworks\Ctq\Model\Quote as QuoteModel;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Hash
 *
 * @package Aheadworks\Ctq\Model\Quote\EntityProcessor
 */
class Hash
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param EncryptorInterface $encryptor
     * @param DateTime $dateTime
     */
    public function __construct(
        EncryptorInterface $encryptor,
        DateTime $dateTime
    ) {
        $this->encryptor = $encryptor;
        $this->dateTime = $dateTime;
    }

    /**
     * Create hash in case it's not existing
     *
     * @param QuoteModel $object
     * @return QuoteModel
     */
    public function beforeSave($object)
    {
        if (!$object->getHash()) {
            $hash = $this->generateHash($object);
            $object->setHash($hash);
        }

        return $object;
    }

    /**
     * After object load empty handler
     *
     * @param QuoteModel $object
     * @return QuoteModel
     */
    public function afterLoad($object)
    {
        return $object;
    }

    /**
     * Create hash
     *
     * @param QuoteModel $quote
     * @return string
     */
    private function generateHash($quote)
    {
        $data = $this->dateTime->timestamp();
        $data .= $quote->getId();
        $data .= $quote->getName();

        return $this->encryptor->hash($data);
    }
}
