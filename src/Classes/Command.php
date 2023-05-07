<?php

namespace App\Classes;

use App\Classes\Translators\GoogleTranslator;
use App\Classes\Translators\TranslatorAbstract;
use RuntimeException;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command
 * @package App\Classes
 */
class Command extends ConsoleCommand
{
    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('csv-to-audio')
            ->setDescription('Grabs audio files from online translator.')
            ->setHelp('This command allows you to grab mp3 files from an online translator with words or sentences from your csv file.')
            ->addArgument('file', InputArgument::REQUIRED, 'csv file name.')
            ->addOption('column', 'c', InputOption::VALUE_OPTIONAL, 'csv file column number [0 - indexed]', 0)
            ->addOption('delimiter', 'd', InputOption::VALUE_OPTIONAL, 'csv file delimiter', ',')
            ->addOption('enclosure', 'e', InputOption::VALUE_OPTIONAL, 'csv file enclosure', '"')
            ->addOption('escape', 's', InputOption::VALUE_OPTIONAL, 'csv file escape', '\\')
            ->addOption('translator', 't', InputOption::VALUE_OPTIONAL, 'translator', 'google')
            ->addOption('language', 'l', InputOption::VALUE_OPTIONAL, 'language code', 'en')
            ->addOption('destination', 'f', InputOption::VALUE_OPTIONAL, 'destination directory', '.')
            ->addOption('overwrite', 'o', InputOption::VALUE_NONE, 'if overwrite existing files');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        try {
            $words = new Words($this->getFile($input), $input->getOption('column'));
            $words->setTranslator($this->getTranslator($input))
                ->setDestinationPath($input->getOption('destination'))
                ->setOverwrite($input->getOption('overwrite'))
                ->setConsoleOutput($output)
                ->grabAudioFiles();

            $returnCode = 0;
        } catch (RuntimeException $e) {
            $this->io->error($e->getMessage());
            $returnCode = $e->getCode();
        }

        return $returnCode;
    }

    /**
     * @param InputInterface $input
     * @return CsvFile
     */
    protected function getFile(InputInterface $input): CsvFile
    {
        $file = $input->getArgument('file');

        $csvFile = new CsvFile($file);
        $csvFile->setCsvControl(
            $input->getOption('delimiter'),
            $input->getOption('enclosure'),
            $input->getOption('escape')
        );

        return $csvFile;
    }

    /**
     * @param InputInterface $input
     * @return TranslatorAbstract
     */
    protected function getTranslator(InputInterface $input): TranslatorAbstract
    {
        switch ($input->getOption('translator')) {
            case 'google':
                $translator = new GoogleTranslator();
                break;

            default:
                throw new RuntimeException(sprintf('Unknown translator: "%s".', $input->getOption('translator')));
        }

        $translator->setLanguage($input->getOption('language'));

        return $translator;
    }
}
