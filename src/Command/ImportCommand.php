<?php

namespace App\Command;

use App\Service\ImportInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'import:ep_members',
    description: 'Import the EP members',
)]
class ImportCommand extends Command
{
    /** @var string the url to retrieve the members list */
    protected string $url;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ImportInterface        $importService,
        string $dataUrl
        //private SerializerInterface $serializer
    )
    {
        parent::__construct();

        $this->url = $dataUrl;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('batch_size', null, 'How many rows to insert at once', 50)
            // for some fucking reason this is considered an ARRAY argument
            ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Don\'t make any actual changes')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite/Update existing records')
            ->addUsage('php bin/console import:ep_members 50 -d -f')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io        = new SymfonyStyle($input, $output);
        $isDryRun  = $input->getOption('dry-run');
        $isForced  = $input->getOption('force');
        $batchSize = $input->getArgument('batch_size');

        if (!$batchSize || $batchSize < 1 || !is_numeric($batchSize) ) {
            $io->error("Batch size is invalid: $batchSize, should be a positive integer");
            return Command::FAILURE;
        }

        $io->info("Importing ". $this->url);
        $io->note("Running in ".($isDryRun ? "Dry" : "Normal")." mode");
        $io->note($isForced ? "Overwriting existing records" : "Skipping existing records");

        $this->entityManager->beginTransaction();

        try {
            $xmlResponse = $this->importService->getRawData($this->url);

            $xmlData = $xmlResponse->getContent();

            $io->note("Xml data is ".strlen($xmlData). " long");

            $classData = new SimpleXMLElement($xmlData);

            $classData = (array)$classData;

            foreach($classData['mep'] as $i => $member) {
                $member = (array)$member;

                if ($this->importService->isStored($member['id']) ) {
                    if($io->isDebug()) {
                        $io->caution("Member ({$member['id']}) already imported");
                    }

                    if ( !$isForced ) {
                        continue;
                    }

                    // TODO: types
                    //$this->importService->updateMemberData($member);
                }

                /*$entity = new Member();
                $entity->setId($member['id'])
                       ->setFullName($member['fullName'])
                       ->setCountry($member['country'])
                       ->setEpPoliticalGroup($member['politicalGroup'])
                       ->setNationalPoliticalGroup($member['nationalPoliticalGroup']);

                $this->entityManager->persist($entity);*/

                if ($i % $batchSize) {
                    $this->entityManager->flush();
                }
            }

            $this->entityManager->flush();

            if($isDryRun) {
                $this->entityManager->rollback();
            }
            else {
                $this->entityManager->commit();
            }
        }
        catch (TransportExceptionInterface $exception) {
            $io->error("Connection error: `{$exception->getMessage()}`");
            $this->entityManager->rollback();

            return Command::FAILURE;
        }
        catch(ORMException $exception) {
            $io->error("Database error: `{$exception->getMessage()}`");
            $this->entityManager->rollback();

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
