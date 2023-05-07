<?php

namespace App\Classes;

use App\Classes\Exceptions\FileException;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use RuntimeException;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem;
use Traversable;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadSheetFactory;

/**
 * Class CsvFile
 * @package App\Classes
 */
class CsvFile implements \IteratorAggregate
{
    /**
     * @var string
     */
    protected string $path;

    /**
     * @var SplFileObject
     */
    protected SplFileObject $file;


    /**
     * CsvFile constructor.
     * @param string $filePathOrName
     * @throws RuntimeException|FileException
     */
    public function __construct(string $filePathOrName)
    {
        $this->setFilePath($filePathOrName);
        $this->convertToCsvIfFormatDiffers();
        $this->file = new SplFileObject($this->path);
        $this->file->setFlags(SplFileObject::READ_CSV);
    }

    /**
     * @return SplFileObject|Traversable
     */
    public function getIterator()
    {
        return $this->file;
    }

    /**
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return void
     */
    public function setCsvControl(string $delimiter, string $enclosure, string $escape): void
    {
        $this->file->setCsvControl($delimiter, $enclosure, $escape);
    }

    /**
     * @param string $filePathOrName
     * @return $this
     */
    protected function setFilePath(string $filePathOrName): CsvFile
    {
        if ((new Filesystem())->isAbsolutePath($filePathOrName)) {
            $this->path = $filePathOrName;
        } else {
            $this->path = COMMAND_DIR . DIRECTORY_SEPARATOR . $filePathOrName;
        }

        if (!is_readable($this->path)) {
            throw new FileException(sprintf('File "%s" doesn\'t exist or is not readable', $this->path));
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function convertToCsvIfFormatDiffers(): CsvFile
    {
        switch (mime_content_type($this->path)) {
            case 'text/plain':
                break;

            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $this->convertXlsToCsv();
                break;

            default:
                throw new FileException(sprintf('Unknown file format of %s', $this->path));

        }

        return $this;
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function convertXlsToCsv(): void
    {
        $xmlFile = pathinfo($this->path);
        $destinationPath = $xmlFile['dirname'] . DIRECTORY_SEPARATOR . $xmlFile['filename'] . '.csv';
        if (is_file($destinationPath)) {
            throw new FileException(sprintf('Can\'t convert to CSV. File %s exists', $destinationPath));
        }

        $reader = SpreadSheetFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($this->path);

        $writer = new CsvWriter($spreadsheet);
        $writer->setEnclosureRequired(false);
        $writer->save($destinationPath);

        $this->path = $destinationPath;
    }
}
