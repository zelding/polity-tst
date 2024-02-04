<?php

namespace App\Command;

use App\Entity\Member;
use App\Model\EpMember;
use App\Model\EpMemberCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use JMS\Serializer\SerializerInterface;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'ImportCommand',
    description: 'Import the EP members',
)]
class ImportCommand extends Command
{
    protected string $url;
    private EntityManager $entityManager;
    private HttpClientInterface $httpClient;
    //private SerializerInterface $serializer;

    public function __construct(
        ContainerInterface $container,
        HttpClientInterface $httpClient,
        //SerializerInterface $serializer
    )
    {
        parent::__construct();

        $this->url = $container->getParameter('ep_url');
        $this->entityManager = $container->get('doctrine.orm.default_entity_manager');
        $this->httpClient = $httpClient;
        //$this->serializer = $serializer;
    }

    protected function configure(): void
    {
        $this
            //->addArgument('batch_size', InputOption::VALUE_OPTIONAL, 'How many rows to insert at once', 50)
            // for some fucking reason this is considered an ARRAY argument
            ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Don\'t make any actual changes')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io        = new SymfonyStyle($input, $output);
        $isDryRun  = $input->getOption('dry-run');
        $batchSize = /*$input->getArgument('batch_size') ??*/ 50;

        if (!$batchSize || $batchSize < 1 || !is_numeric($batchSize) ) {
            $io->error("Batch size is invalid: $batchSize, should be a positive integer");
        }

        $io->info("Importing ". $this->url);
        $io->note("Running in ".($isDryRun ? "Dry" : "Normal")." mode");

        $this->entityManager->beginTransaction();

        try {
            $xmlResponse = $this->httpClient->request("GET", $this->url, []);

            $xmlData = $xmlResponse->getContent(true);

            $io->note("Xml data is ".strlen($xmlData). " long");

            $classData = new SimpleXMLElement($xmlData);

            $classData = (array)$classData;

            foreach($classData['mep'] as $i => $member) {
                $member = (array)$member;

                $entity = new Member();
                $entity->setId($member['id'])
                       ->setFullName($member['fullName'])
                       ->setCountry($member['country'])
                       ->setEpPoliticalGroup($member['politicalGroup'])
                       ->setNationalPoliticalGroup($member['nationalPoliticalGroup']);

                $this->entityManager->persist($entity);

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
        }
        catch(ORMException $exception) {
            $this->entityManager->rollback();
        }

        return Command::SUCCESS;
    }
}
