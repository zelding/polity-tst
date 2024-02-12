<?php

namespace App\Command;

use App\Message\MemberImportMessage;
use App\Service\ImportInterface;
use Exception;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
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
        private readonly ImportInterface        $importService,
        private readonly MessageBusInterface    $messageBus,
        string $dataUrl
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
            ->addUsage('-d -f 50')
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
            return Command::INVALID;
        }

        $io->info("Importing ". $this->url);
        $io->note("Running in ".($isDryRun ? "Dry" : "Normal")." mode");
        $io->note($isForced ? "Overwriting existing records" : "Skipping existing records");

        try {
            $xmlResponse = $this->importService->getRawData($this->url);

            $xmlData = $xmlResponse->getContent();

            $io->note("Xml data is ".strlen($xmlData). " long");

            try {
                $classData = new SimpleXMLElement($xmlData);
            }
            catch(Exception $exception) {
                $io->error($exception->getMessage());
                return Command::FAILURE;
            }

            $pb = $io->createProgressBar($classData->mep->count());

            foreach($classData->mep as $member) {
                $memberModel = $this->importService->parseRawData($member->asXML());

                if($isDryRun) {
                    //$io->write(
                    //    sprintf("Would dispatch member: %s - %s", $memberModel->getFullName(), $memberModel->getCountry())
                    //);
                    $pb->advance();
                    continue;
                }

                $this->messageBus->dispatch(
                    new MemberImportMessage($memberModel, $isForced)
                );

                $pb->advance();
            }
        }
        catch (TransportExceptionInterface $exception) {
            $io->error("Connection error: `{$exception->getMessage()}`");

            return Command::FAILURE;
        }
        finally {
            $pb->finish();
            $io->writeln("");
        }

        return Command::SUCCESS;
    }
}
