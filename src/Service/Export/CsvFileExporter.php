<?php

namespace App\Service\Export;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CsvFileExporter implements FileExportInterface
{
    /**
     * @var array <Task>
     */
    private $tasks;

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

    public function export(): BinaryFileResponse
    {
        // TODO: Implement export() method.
    }
}