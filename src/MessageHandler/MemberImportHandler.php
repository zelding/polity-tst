<?php

namespace App\MessageHandler;

use App\Exception\AppException;
use App\Message\MemberImportMessage;
use App\Service\ImportInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
class MemberImportHandler
{
    public function __construct(
        protected ImportInterface $importService,
        protected readonly EntityManagerInterface $entityManager
    )
    {}

    public function __invoke(MemberImportMessage $importMessage): void
    {
        try {
            if ($importMessage->allowUpdate) {
                $this->importService->updateMemberData($importMessage->getMemberData());

                return;
            }

            $this->importService->storeNewEpMember($importMessage->getMemberData());
        }
        //TODO: log or something
        catch (AppException|ORMException $exception) {
            if ($exception instanceof ORMException) {
                $this->entityManager->rollback();
            }

            throw new UnrecoverableMessageHandlingException(previous: $exception);
        }
    }
}
