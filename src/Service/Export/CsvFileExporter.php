<?php

namespace App\Service\Export;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
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
        if ($this->tasks === null) {
            throw new RuntimeException('Tasks should be set before export');
        }

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        $response = new Response($serializer->encode($this->getPreparedArray(), 'csv'));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$this->getFileName().'.csv');

        return $response;
    }
}
