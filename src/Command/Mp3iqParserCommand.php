<?php

namespace App\Command;

use App\Service\Mp3iqParser;
use App\Util\WatchableTrait;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Mp3iqParserCommand extends Command
{
    use LockableTrait, WatchableTrait;

    protected const PAGE_PARAM = 'page';

    public function __construct(
        protected Mp3iqParser $parser
    ) {
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('parse:mp3iq')
            ->addOption(
                self::PAGE_PARAM,
                'p',
                InputOption::VALUE_REQUIRED,
                'page number',
                1
            )
        ;
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // TODO: auto checkIsParserRunning + startParser - without name

        // блокируем команду от параллельного выполнения
        if (!$this->lock()) {
            $io->warning('Эта команда сейчас выполняется в другом процессе, нужно подождать завершения.');

            return Command::FAILURE;
        }

        $this->startStopwatch();

        $page = (int) $input->getOption(self::PAGE_PARAM);
        $this->parser->parseFileLinks($page);

        $io->newLine();
        $io->success($this->getStopwatchInfo());

        // снимаем блокировку от параллельного выполнения
        $this->release();

        return Command::SUCCESS;
    }
}