<?php

namespace App\Service\Export;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface FileExportInterface
{
    /**
     * @param array <Task> $tasks
     *
     * @return $this
     */
    public function setTasks(array $tasks): self;

    /**
     * @return BinaryFileResponse
     */
    public function export(): BinaryFileResponse;
}