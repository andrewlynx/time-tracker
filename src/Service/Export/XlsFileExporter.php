<?php

namespace App\Service\Export;

use Symfony\Component\HttpFoundation\Response;

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

    public function export(): ?Response
    {
        // TODO: Implement export() method.
    }
}
