<?php

namespace App\Service\Export;

use App\Entity\Task;
use Symfony\Component\HttpFoundation\Response;

interface FileExportInterface
{
    /**
     * @param array <Task> $tasks
     *
     * @return $this
     */
    public function setTasks(array $tasks): self;

    /**
     * @param string $author
     *
     * @return $this
     */
    public function setAuthor(string $author): self;

    /**
     * @param string $startDate
     *
     * @return $this
     */
    public function setStartDate(string $startDate): self;

    /**
     * @param string $endDate
     *
     * @return $this
     */
    public function setEndDate(string $endDate): self;

    /**
     * @return Response|null
     */
    public function export(): ?Response;
}
