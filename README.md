# csv-to-audio

A simple command which lets you convert words or phrases from CSV file's column into audio files using Google's translator.
I found it useful while creating language courses for myself in Memrise.
Source file may be an XLSX spreadsheet as well, and it will be converted to CSV during the process.

## Requirements

PHP >= 7.4

php.ini

`phar.readonly = Off`

## Installation

Run `./install.sh` and then copy the built file.

```bash
sudo cp -f ./build/csv-to-audio.phar /usr/local/bin/csv-to-audio
sudo chmod a+x /usr/local/bin/csv-to-audio
``` 

## Usage

```bash
csv-to-audio file.csv
```
#### Available options

| Option          | Short version | Meaning                              | Default           |
|-----------------|---------------|--------------------------------------|-------------------|
| `--column`      | `-c`          | csv file column number (0 - indexed) | 0                 |
| `--delimiter`   | `-d`          | csv file delimiter                   | ,                 |
| `--enclosure`   | `-e`          | csv file enclosure                   | "                 |
| `--escape`      | `s`           | csv file escape                      | \                 |
| `--translator`  | `-t`          | translator (only Google for now)     | google            |
| `--language`    | `-l`          | language code                        | en                |
| `--destination` | `-f`          | destination directory                | working directory |
| `--overwrite`   | `-o`          | if overwrite existing files          | false             |
