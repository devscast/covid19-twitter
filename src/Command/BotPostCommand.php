<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Covid19Service;
use App\Service\TwitterService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class BotPostCommand
 * @package App\Command
 * @author bernard-ng <ngandubernard@gmail.com>
 */
class BotPostCommand extends Command
{
    protected static $defaultName = 'bot:post';
    private LoggerInterface $logger;
    private Covid19Service $covid19;
    private TwitterService $twitter;

    /**
     * BotPostCommand constructor.
     * @param Covid19Service $covid19
     * @param TwitterService $twitter
     * @param LoggerInterface $logger
     * @author bernard-ng <ngandubernard@gmail.com>
     */
    public function __construct(Covid19Service $covid19, TwitterService $twitter, LoggerInterface $logger)
    {
        parent::__construct();
        $this->logger = $logger;
        $this->covid19 = $covid19;
        $this->twitter = $twitter;
    }

    /**
     * @author bernard-ng <ngandubernard@gmail.com>
     */
    protected function configure()
    {
        $this->setDescription('Post a covid update review to twitter');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @author bernard-ng <ngandubernard@gmail.com>
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $status = $this->covid19->getConfirmedCase();


            dd($status);

            $this->twitter->post($status);

            $io->success('Posted');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->logger->error($e, $e->getTrace());
            return Command::FAILURE;
        }
    }
}
