<?php

namespace App\Classes;

use RuntimeException;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem;
use Traversable;

/**
 * Class CsvFile
 * @package App\Classes
 */
class CsvFile implements \IteratorAggregate
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var SplFileObject
     */
    protected $file;


    /**
     * CsvFile constructor.
     * @param string $filePathOrName
     * @throws RuntimeException
     */
    public function __construct(string $filePathOrName)
    {
        $this->setFilePath($filePathOrName);
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
    public function setCsvControl($delimiter, $enclosure, $escape): void
    {
        $this->file->setCsvControl($delimiter, $enclosure, $escape);
    }

    /**
     * @param string $filePathOrName
     * @return void
     */
    protected function setFilePath(string $filePathOrName): void
    {
        if ((new Filesystem())->isAbsolutePath($filePathOrName)) {
            $this->path = $filePathOrName;
        } else {
            $this->path = COMMAND_DIR . DIRECTORY_SEPARATOR . $filePathOrName;
        }
    }
}
