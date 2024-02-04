<?php

namespace App\Command;

use App\Entity\Member;
use App\Entity\MemberContact;
use App\Model\MemberContactType;
use App\Repository\MemberContactRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function Symfony\Component\String\b;

#[AsCommand(
    name: 'ScrapeCommand',
    description: 'Scrape the Contact info of the already imported members',
)]
class ScrapeCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly MemberRepository $memberRepository,
        private readonly MemberContactRepository $contactRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Don\'t make any actual changes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io       = new SymfonyStyle($input, $output);
        $isDryRun = $input->getOption('dry-run');
        $batchSize = 50; //not going to bother tying again

        $this->entityManager->beginTransaction();

        try {
            /** @var array<Member> $members */
            $members = $this->memberRepository->findAll();

            if ( count($members) < 1) {
                $io->error("Members are not imported yet");

                return Command::FAILURE;
            }

            $i = 0;
            foreach($members as $member) {
                if (!$this->memberRepository->find($member->getId()) ) {
                    $io->caution("Member ({$member->getId()}) doesn't exists, skipping");
                    continue;
                }

                $url = sprintf("https://www.europarl.europa.eu/meps/en/%d", $member->getId());

                $response = $this->httpClient->request('GET', $url);

                $crawler = new Crawler($response->getContent());

                $contactsBlock = $crawler->filter('#contacts');
                $contactRows   = $contactsBlock->filter('.card');

                foreach($contactRows->getIterator() as $card) {
                    $matches = [];
                    preg_match_all("~\t*([^\t\n]*)\n~", $card->nodeValue, $matches, PREG_PATTERN_ORDER);

                    $matches = array_values(array_filter($matches[1], function ($i) {
                        return trim($i);
                    }));

                    //TODO: check is contact already saved

                    $contact = new MemberContact();
                    $contact->setMember($member);
                    $contact->setType(MemberContactType::Address);
                    $contact->setContactData($matches[1]);

                    $this->entityManager->persist($contact);
                    $i++;
                }

                // handle telephone and social? I didn't see any social links in the few examples I saw

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

        $io->success("Imported $i contacts");

        return Command::SUCCESS;
    }
}
