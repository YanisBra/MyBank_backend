<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Operation as OperationEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class OperationDataPersister implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof OperationEntity) {
            return $data;
        }

        if (null === $data->getUser()) {
            $user = $this->security->getUser();
            $data->setUser($user);
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }

    public function remove(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof OperationEntity) {
            $this->em->remove($data);
            $this->em->flush();
        }
    }
}