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
 * @package    CreditLimit
 * @version    1.3.3
 * @copyright  Copyright (c) 2025 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Service;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\TransactionManagementInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Api\TransactionRepositoryInterface;
use Aheadworks\CreditLimit\Model\ResourceModel\Transaction as TransactionResource;
use Aheadworks\CreditLimit\Model\Transaction\CompositeBuilder as TransactionCompositeBuilder;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Model\Customer\Notifier;
use Magento\Framework\Exception\ValidatorException;

/**
 * Class TransactionService
 *
 * @package Aheadworks\CreditLimit\Model\Service
 */
class TransactionService implements TransactionManagementInterface
{
    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var TransactionResource
     */
    private $transactionResource;

    /**
     * @var TransactionCompositeBuilder
     */
    private $transactionCompositeBuilder;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * TransactionService constructor.
     * @param TransactionRepositoryInterface $transactionRepository
     * @param TransactionResource $transactionResource
     * @param TransactionCompositeBuilder $transactionCompositeBuilder
     * @param Notifier $notifier
     */
    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        TransactionResource $transactionResource,
        TransactionCompositeBuilder $transactionCompositeBuilder,
        Notifier $notifier
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionResource = $transactionResource;
        $this->transactionCompositeBuilder = $transactionCompositeBuilder;
        $this->notifier = $notifier;
    }

    /**
     * Create transaction
     *
     * List of params:
     * customer_id - required
     * action - required
     * amount - depends on action
     * amount_currency - depends on action
     * used_currency - depends on action
     * credit_limit - depends on action
     * other params are optional
     *
     * @param TransactionParametersInterface $params
     * @return TransactionInterface
     * @throws LocalizedException
     */
    public function createTransaction(TransactionParametersInterface $params): TransactionInterface
    {
        try {
            $this->transactionResource->beginTransaction();
            $transaction = $this->transactionCompositeBuilder->build($params);
            $this->transactionRepository->save($transaction);
            $this->transactionResource->commit();
            $this->notifier->notify($params->getCustomerId(), $transaction);
        } catch (ValidatorException $e) {
            $this->transactionResource->rollBack();
            throw $e;
        } catch (\Exception $e) {
            $this->transactionResource->rollBack();
            throw new LocalizedException(__($e->getMessage()));
        }

        return $transaction;
    }

    /**
     * Create multiple transactions
     *
     * @param TransactionParametersInterface[] $paramsList
     * @return void
     * @throws LocalizedException
     */
    public function createMultipleTransaction(array $paramsList): void
    {
        try {
            $transactions = [];
            foreach ($paramsList as $key => $params) {
                $transactions[$key] = $this->transactionCompositeBuilder->build($params);
            }

            if (count($transactions)) {
                $this->transactionResource->saveMultiple($transactions);
            }

            foreach ($paramsList as $key => $params) {
                if (isset($transactions[$key])) {
                    $this->notifier->notify($params->getCustomerId(), $transactions[$key]);
                }
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
