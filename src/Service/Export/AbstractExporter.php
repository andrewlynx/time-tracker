<?php

namespace App\Service\Export;

use App\Entity\Task;
use RuntimeException;

class AbstractExporter
{
    public const DEFAULT_FILENAME = 'document';

    /**
     * @var string
     */
    protected $author;

    /**
     * @var string
     */
    protected $startDay;

    /**
     * @var string
     */
    protected $endDay;

    /**
     * @var array <Task>
     */
    protected $tasks;

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
     * @param string $author
     *
     * @return $this
     */
    public function setAuthor(string $author): FileExportInterface
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @param string $startDate
     *
     * @return $this
     */
    public function setStartDate(string $startDate): FileExportInterface
    {
        $this->startDay = $startDate;

        return $this;
    }

    /**
     * @param string $endDate
     *
     * @return $this
     */
    public function setEndDate(string $endDate): FileExportInterface
    {
        $this->endDay = $endDate;

        return $this;
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return isset($this->startDay, $this->endDay)
            ? sprintf('report %s - %s', $this->startDay, $this->endDay)
            : self::DEFAULT_FILENAME;
    }

    /**
     * @return string
     */
    protected function getTotalTime(): string
    {
        if ($this->tasks === null) {
            throw new RuntimeException('Tasks should be set before calculating totals');
        }

        $total = 0;
        /** @var Task $task */
        foreach ($this->tasks as $task) {
            $total += $task->getTimeSpent();
        }

        return sprintf('%s hour(s) %s minute(s)', intdiv($total, 60), $total % 60);
    }
}