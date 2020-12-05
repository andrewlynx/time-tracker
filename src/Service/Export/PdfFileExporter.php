<?php

namespace App\Service\Export;

use Dompdf\Dompdf;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PdfFileExporter extends AbstractExporter implements FileExportInterface
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(ContainerInterface $container)
    {
        $this->twig = $container->get('twig');
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function export(): ?Response
    {
        $domPdf = new Dompdf();

        $html = $this->twig->render('task/pdf/file.html.twig', [
            'author' => $this->author,
            'tasks' => $this->tasks,
            'startDay' => $this->startDay,
            'endDay' => $this->endDay,
            'total' => $this->getTotalTime(),
        ]);
        $domPdf->loadHtml($html);
        $domPdf->render();

        $domPdf->stream($this->getFileName());
    }
}
