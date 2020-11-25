<?php

namespace App\Service\Export;

use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PdfFileExporter implements FileExportInterface
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
        if ($this->tasks === null) {
            throw new RuntimeException('Tasks should be set before export');
        }

        dd($this->tasks);
    }
}