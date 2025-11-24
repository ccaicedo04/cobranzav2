<?php

namespace Core;

use RuntimeException;

class SimpleZip
{
    /**
     * @var array<int, array{name: string, data: string, time: int}>
     */
    private $entries = [];

    /**
     * @return void
     */
    public function addFile(string $name, string $contents)
    {
        $name = ltrim(str_replace('\\', '/', $name), '/');
        if ($name === '' || str_ends_with($name, '/')) {
            throw new RuntimeException('El nombre del archivo ZIP no es válido: ' . $name);
        }

        $this->entries[] = [
            'name' => $name,
            'data' => $contents,
            'time' => time(),
        ];
    }

    public function toString(): string
    {
        $output = '';
        $centralDirectory = '';
        $offset = 0;

        foreach ($this->entries as $entry) {
            [$dosTime, $dosDate] = $this->dosTime($entry['time']);
            $data = $entry['data'];
            $crc = crc32($data);
            $compressed = gzcompress($data, 9);
            if ($compressed === false) {
                throw new RuntimeException('No fue posible comprimir el contenido del archivo ZIP.');
            }

            $compressed = substr($compressed, 2, -4); // raw deflate
            $compressedLength = strlen($compressed);
            $uncompressedLength = strlen($data);
            $nameLength = strlen($entry['name']);

            $localHeader = pack(
                'VvvvvvVVVvv',
                0x04034b50,
                20,
                0,
                8,
                $dosTime,
                $dosDate,
                $crc,
                $compressedLength,
                $uncompressedLength,
                $nameLength,
                0
            );

            $output .= $localHeader . $entry['name'] . $compressed;

            $centralHeader = pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014b50,
                0x0014,
                20,
                0,
                8,
                $dosTime,
                $dosDate,
                $crc,
                $compressedLength,
                $uncompressedLength,
                $nameLength,
                0,
                0,
                0,
                0,
                0,
                $offset
            );

            $centralDirectory .= $centralHeader . $entry['name'];
            $offset += strlen($localHeader) + $nameLength + $compressedLength;
        }

        $centralDirectorySize = strlen($centralDirectory);
        $endOfCentralDirectory = pack(
            'VvvvvVVv',
            0x06054b50,
            0,
            0,
            count($this->entries),
            count($this->entries),
            $centralDirectorySize,
            $offset,
            0
        );

        return $output . $centralDirectory . $endOfCentralDirectory;
    }

    /**
     * @return array{0:int,1:int}
     */
    private function dosTime(int $timestamp): array
    {
        $timestamp = max(315532800, $timestamp); // 1980-01-01
        $date = getdate($timestamp);
        $dosDate = (($date['year'] - 1980) << 9)
            | ($date['mon'] << 5)
            | $date['mday'];
        $dosTime = ($date['hours'] << 11)
            | ($date['minutes'] << 5)
            | ((int) ($date['seconds'] / 2));

        return [$dosTime, $dosDate];
    }
}
