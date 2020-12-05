<?php

namespace App\Service\Export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception as PhpOfficeException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class XlsFileExporter extends AbstractExporter implements FileExportInterface
{
    /**
     * @param array $tasks
     *
     * @return $this
     */
    public function setTasks(array $tasks): FileExportInterface
    {
        $this->tasks = $tasks;

        return $this;
    }

    /**
     * @return Response|null
     *
     * @throws ExceptionInterface
     * @throws PhpOfficeException
     */
    public function export(): ?Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(sprintf('Report %s', $this->author));

        $sheet->fromArray($this->getPreparedArray());

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $this->getFileName());
        $writer->save($tempFile);

        $response = new BinaryFileResponse($tempFile);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $this->getFileName().'.xlsx'
        );

        return $response;
    }

    /**
     * @return array
     *
     * @throws ExceptionInterface
     */
    private function getPreparedArray(): array
    {
        $serializer = new Serializer([new ObjectNormalizer()]);

        return array_merge(
            $serializer->normalize(
                $this->tasks,
                'array',
                [AbstractNormalizer::ATTRIBUTES => self::EXPORT_FIELDS]
            ),
            [[$this->getTotalTime()]]
        );
    }
}
