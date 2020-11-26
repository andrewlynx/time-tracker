<?php

namespace App\Service\Export;

use App\Entity\Task;
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

        $prepared = [];
        /** @var Task $task */
        foreach ($this->tasks as $task) {
            $prepared[] = [
                'Title' => $task->getTitle(),
                'Date' => $task->getDate(),
                'Spent Time, min' => $task->getTimeSpent(),
                'Comment' => $task->getComment(),
            ];
        }
        $prepared[] = [
            'Title' => 'Total time',
            'Spent Time, min' => $this->getTotalTime(),
        ];

        $response = new Response($serializer->encode($prepared, 'csv'));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$this->getFileName().'.csv');

        return $response;
    }
}