<?php

namespace App\Service\Export;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CsvFileExporter extends AbstractExporter implements FileExportInterface
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
     */
    public function export(): ?Response
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        $csv = $serializer->serialize(
            $this->tasks,
            'csv',
            [AbstractNormalizer::ATTRIBUTES => self::EXPORT_FIELDS]
        );
        // Add total time to the last line
        $csv .= $this->getTotalTime().PHP_EOL;

        $response = new Response($csv);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$this->getFileName().'.csv');

        return $response;
    }
}
