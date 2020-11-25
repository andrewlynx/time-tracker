<?php

use Doctrine\ORM\Query;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class PaginatorFactory
{
    /**
     * @param Query $query
     * @param int   $page
     * @param int   $itemsPerPage
     *
     * @return Pagerfanta<mixed>
     */
    public static function createPaginator(Query $query, int $page, int $itemsPerPage = 5): Pagerfanta
    {
        $paginator = new Pagerfanta(new QueryAdapter($query));
        $paginator->setMaxPerPage($itemsPerPage);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}