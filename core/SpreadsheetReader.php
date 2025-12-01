<?php

namespace Core;

use SimpleXMLElement;
use ZipArchive;

class SpreadsheetReader
{
    public static function rows(string $path): array
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($extension === 'csv') {
            return self::readCsv($path);
        }

        if ($extension === 'xlsx') {
            return self::readXlsx($path);
        }

        return [];
    }

    private static function readCsv(string $path): array
    {
        $rows = [];
        if (!is_readable($path)) {
            return $rows;
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return $rows;
        }

        $headers = null;
        while (($data = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                $headers = array_map([self::class, 'sanitizeHeader'], $data);
                continue;
            }

            $row = [];
            foreach ($headers as $index => $key) {
                $row[$key] = $data[$index] ?? '';
            }
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private static function readXlsx(string $path): array
    {
        $rows = [];
        if (!class_exists(ZipArchive::class)) {
            return $rows;
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return $rows;
        }

        $sharedStrings = [];
        $shared = $zip->getFromName('xl/sharedStrings.xml');
        if ($shared) {
            $xml = new SimpleXMLElement($shared);
            foreach ($xml->si as $si) {
                $texts = [];
                foreach ($si->t as $t) {
                    $texts[] = (string) $t;
                }
                $sharedStrings[] = implode('', $texts);
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetXml) {
            $zip->close();
            return $rows;
        }

        $sheet = new SimpleXMLElement($sheetXml);
        $headers = null;
        foreach ($sheet->sheetData->row as $row) {
            $rowValues = [];
            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                $colIndex = self::columnIndexFromRef($ref);
                $value = '';
                if (isset($cell->v)) {
                    $type = (string) $cell['t'];
                    $raw = (string) $cell->v;
                    if ($type === 's') {
                        $value = $sharedStrings[(int) $raw] ?? '';
                    } else {
                        $value = $raw;
                    }
                } elseif (isset($cell->is)) {
                    $value = (string) $cell->is->t;
                }
                $rowValues[$colIndex] = $value;
            }

            if ($headers === null) {
                ksort($rowValues);
                $headers = array_map([self::class, 'sanitizeHeader'], $rowValues);
                continue;
            }

            if (!$headers) {
                continue;
            }

            $rowAssoc = [];
            foreach ($headers as $index => $key) {
                $rowAssoc[$key] = $rowValues[$index] ?? '';
            }
            $rows[] = $rowAssoc;
        }

        $zip->close();

        return $rows;
    }

    private static function sanitizeHeader(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace([' ', '#', '.', '-'], '_', $value);

        return $value;
    }

    private static function columnIndexFromRef(string $ref): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($ref));
        $index = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }

        return max(0, $index - 1);
    }
}
