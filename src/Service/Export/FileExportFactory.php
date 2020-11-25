<?php

namespace App\Service\Export;

class FileExportFactory
{
    public const FORMAT_PDF = 'pdf';
    public const FORMAT_CSV = 'csv';
    public const FORMAT_XLS = 'xls';

    public const FORMATS = [
        self::FORMAT_PDF,
        self::FORMAT_CSV,
        self::FORMAT_XLS,
    ];

    /**
     * @param string $type
     *
     * @return FileExportInterface
     */
    public static function getFileExporter(string $type): FileExportInterface
    {
        switch ($type) {
            case self::FORMAT_PDF:

                return new PdfFileExporter();
            case self::FORMAT_CSV:

                return new CsvFileExporter();
            case self::FORMAT_XLS:

                return new XlsFileExporter();
            default:
                throw new \InvalidArgumentException(sprintf('Unknown format for file export: %s', $type));
        }
    }
}