<?php

namespace App\MessageHandler;

use App\Message\MemberImportMessage;
use App\Service\ImportInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MemberImportHandler
{
    public function __construct(
        protected ImportInterface $importService
    )
    {}

    public function __invoke(MemberImportMessage $importMessage): void
    {
        if ( $this->importService->isStored($importMessage->getMemberData()->getId()) ) {
            if ( !$importMessage->allowUpdate ) {
                // TODO: log or something
                return;
            }

            $this->importService->updateMemberData($importMessage->getMemberData());
            return;
        }

        $this->importService->storeNewEpMember($importMessage->getMemberData());
    }
}
