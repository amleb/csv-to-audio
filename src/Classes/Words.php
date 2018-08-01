<?php

namespace App\Classes;

use App\Classes\Translators\TranslatorAbstract;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class Words
 * @package App\Classes
 */
class Words
{
    /**
     * @var CsvFile
     */
    protected $file;

    /**
     * @var int
     */
    protected $columnNumber;

    /**
     * @var OutputInterface
     */
    protected $consoleOutput;

    /**
     * @var TranslatorAbstract
     */
    protected $translator;

    /**
     * @var string
     */
    protected $destinationPath;

    /**
     * @var bool
     */
    protected $overwrite;

    /**
     * Words constructor.
     * @param CsvFile $file
     * @param int $columnNumber
     */
    public function __construct(CsvFile $file, int $columnNumber)
    {
        $this->file = $file;
        $this->columnNumber = $columnNumber;
    }

    /**
     * @param TranslatorAbstract $translator
     * @return Words
     */
    public function setTranslator(TranslatorAbstract $translator): Words
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @param string $destinationPath
     * @throws RuntimeException
     * @return Words
     */
    public function setDestinationPath(string $destinationPath): Words
    {
        $this->destinationPath = $this->resolveDestinationPath($destinationPath) . DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * @param bool $overwrite
     * @return Words
     */
    public function setOverwrite(bool $overwrite): Words
    {
        $this->overwrite = $overwrite;
        return $this;
    }

    /**
     * @param OutputInterface $consoleOutput
     * @return Words
     */
    public function setConsoleOutput(OutputInterface $consoleOutput): Words
    {
        $this->consoleOutput = $consoleOutput;
        return $this;
    }

    /**
     * @return void
     */
    public function grabAudioFiles(): void
    {
        foreach ($this->file as $row) {
            if (!array_key_exists($this->columnNumber, $row)) {
                throw new RuntimeException('Unknown column ' . $this->columnNumber);
            }

            if (trim($row[$this->columnNumber])) {
                $word = $this->prepareWord($row[$this->columnNumber]);
                if ($word) {
                    $this->grabAudioFile($word);
                }
            } else {
                $this->consoleOutput->writeln('Skipping empty string.');
            }

        }
    }

    /**
     * @param string $word
     */
    protected function grabAudioFile(string $word): void
    {
        $destinationFile = $this->prepareDestinationFile($word);

        if ($this->overwrite || !file_exists($destinationFile)) {
            if ($this->translator->grab($word, $destinationFile)) {
                $this->consoleOutput->writeln(sprintf('Saving "%s".', $destinationFile));
            } else {
                $this->consoleOutput->writeln($this->translator->getLastError());
            }
        } else {
            $this->consoleOutput->writeln(sprintf('Skipping "%s" - file exists.', $destinationFile));
        }
    }

    /**
     * @param string $word
     * @return string
     */
    protected function prepareWord(string $word): string
    {
        $word = preg_replace('/\b([^ ]+)\/([^ ]+)\b/', '$1', $word);
        $word = preg_replace('/\bsb\b/', 'somebody', $word);
        return trim(preg_replace('/\bsth\b/', 'something', $word));
    }

    /**
     * @param string $word
     * @return string
     */
    protected function prepareDestinationFile(string $word): string
    {
        $word = preg_replace('/\s+/', '_', strtolower($word));
        $filename = preg_replace('/[^a-z0-9\._]/', '', $word);
        return $this->destinationPath . $filename . '.' . $this->translator->getFileExtension();
    }

    /**
     * @param string $path
     * @throws RuntimeException
     * @return string
     */
    protected function resolveDestinationPath(string $path): string
    {
        if ($path === '.' || $path === './') {
            $destination = COMMAND_DIR;
        } elseif ((new Filesystem())->isAbsolutePath($path)) {
            $destination = $path;
        } else {
            $destination = COMMAND_DIR . DIRECTORY_SEPARATOR . $path;
        }

        if (!is_writable($destination)) {
            throw new RuntimeException(sprintf('Destination "%s" is not writable.', $destination));
        }

        return $destination;
    }
}
