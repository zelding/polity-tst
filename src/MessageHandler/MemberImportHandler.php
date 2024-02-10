<?php

namespace App\MessageHandler;

use App\Exception\AppException;
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
        try {
            if ($importMessage->allowUpdate) {
                $this->importService->updateMemberData($importMessage->getMemberData());

                return;
            }

            $this->importService->storeNewEpMember($importMessage->getMemberData());
        }
        catch (AppException $exception) {
            //TODO: log or something
            //maybe there is a nicer way to send them to the failed queue
        }

        /*
        if ( $this->importService->isStored($importMessage->getMemberData()->getId()) ) {
            if ( $importMessage->allowUpdate ) {
                // TODO: log or something
                return;
            }

            $this->importService->updateMemberData($importMessage->getMemberData());
            return;
        }

        $this->importService->storeNewEpMember($importMessage->getMemberData());
        */
    }
}
