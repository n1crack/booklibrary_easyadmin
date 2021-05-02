<?php
namespace App\Service;

class BookPaginator
{
    public const PAGINATOR_PER_PAGE = 5;

    public function get($page, $count)
    {
        return [
          'count' => $count,
          'page_count' => (int) ceil($count / self::PAGINATOR_PER_PAGE),
          'page_size' => self::PAGINATOR_PER_PAGE,
          'from' => 1 + ($page - 1) * self::PAGINATOR_PER_PAGE,
          'to' => min($page * self::PAGINATOR_PER_PAGE, $count ),
          'current_page' => $page,
          'previous' => max($page - 1, 1),
          'next' => (int) min(ceil($count / self::PAGINATOR_PER_PAGE), $page + 1)
        ];
    }
}