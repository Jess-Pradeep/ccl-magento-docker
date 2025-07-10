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
namespace Aheadworks\Ca\Model\Email\Metadata\Builder\Modifier\RecipientData;

use Aheadworks\Ca\Model\Email\EmailMetadataInterface;
use Aheadworks\Ca\Model\Magento\ModuleUser\UserRepository;
use Aheadworks\Ca\Api\Data\CompanyInterface;
use Aheadworks\Ca\Model\Email\Metadata\Builder\ModifierInterface;

/**
 * Class SalesRepresentative
 *
 * @package Aheadworks\Ca\Model\Email\Metadata\Builder\Modifier\RecipientData
 */
class SalesRepresentative implements ModifierInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata(EmailMetadataInterface $emailMetadata, array $relatedObjectList): EmailMetadataInterface
    {
        $company = $relatedObjectList[ModifierInterface::COMPANY];
        $emailMetadata->setRecipientEmail($this->getRecipientEmail($company));
        $emailMetadata->setRecipientName($this->getRecipientName($company));

        return $emailMetadata;
    }

    /**
     * Retrieve recipient name
     *
     * @param CompanyInterface $company
     * @return string
     */
    private function getRecipientName($company)
    {
        try {
            $user = $this->userRepository->getById($company->getSalesRepresentativeId());
            $name = $user->getFirstName() . ' ' .  $user->getLastName();
        } catch (\Exception $e) {
            $name = '';
        }

        return $name;
    }

    /**
     * Retrieve recipient email
     *
     * @param CompanyInterface $company
     * @return string
     */
    private function getRecipientEmail($company)
    {
        try {
            $user = $this->userRepository->getById($company->getSalesRepresentativeId());
            $email = $user->getEmail();
        } catch (\Exception $e) {
            $email = '';
        }

        return $email;
    }
}
