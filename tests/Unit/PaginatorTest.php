<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Pagination\Paginator;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    public function testPaginationCalculations(): void
    {
        $items = ['a', 'b', 'c'];
        $paginator = new Paginator($items, 25, 10, 2);

        $this->assertSame($items, $paginator->items());
        $this->assertSame(25, $paginator->total());
        $this->assertSame(10, $paginator->perPage());
        $this->assertSame(2, $paginator->currentPage());
        $this->assertSame(3, $paginator->lastPage());
        $this->assertTrue($paginator->hasPreviousPage());
        $this->assertTrue($paginator->hasNextPage());
        $this->assertSame(1, $paginator->previousPage());
        $this->assertSame(3, $paginator->nextPage());

        $array = $paginator->toArray();
        $this->assertSame(25, $array['meta']['total']);
        $this->assertSame(3, $array['meta']['last_page']);
    }

    public function testPaginationFirstPage(): void
    {
        $paginator = new Paginator(['item'], 5, 5, 1);

        $this->assertFalse($paginator->hasPreviousPage());
        $this->assertFalse($paginator->hasNextPage());
        $this->assertSame(1, $paginator->lastPage());
    }
}
