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
     * @var PdfFileExporter
     */
    private $pdfFileExporter;

    /**
     * @var CsvFileExporter
     */
    private $csvFileExporter;

    /**
     * @var XlsFileExporter
     */
    private $xlsFileExporter;

    /**
     * @param PdfFileExporter $pdfFileExporter
     * @param CsvFileExporter $csvFileExporter
     * @param XlsFileExporter $xlsFileExporter
     */
    public function __construct(
        PdfFileExporter $pdfFileExporter,
        CsvFileExporter $csvFileExporter,
        XlsFileExporter $xlsFileExporter
    ) {
        $this->pdfFileExporter = $pdfFileExporter;
        $this->csvFileExporter = $csvFileExporter;
        $this->xlsFileExporter = $xlsFileExporter;
    }

    /**
     * @param string $type
     *
     * @return FileExportInterface
     */
    public function getFileExporter(string $type): FileExportInterface
    {
        switch ($type) {
            case self::FORMAT_PDF:
                return $this->pdfFileExporter;
            case self::FORMAT_CSV:
                return $this->csvFileExporter;
            case self::FORMAT_XLS:
                return $this->xlsFileExporter;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown format for file export: %s', $type));
        }
    }
}
