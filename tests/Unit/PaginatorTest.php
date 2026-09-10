<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;
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
        $this->assertTrue(!$paginator->onFirstPage());
        $this->assertTrue($paginator->hasMorePages());
        $this->assertSame(1, $paginator->currentPage() - 1);
        $this->assertSame(3, $paginator->currentPage() + 1);

        $array = $paginator->toArray();
        $this->assertSame(25, $array['total']);
        $this->assertSame(3, $array['last_page']);
    }

    public function testPaginationFirstPage(): void
    {
        $paginator = new Paginator(['item'], 5, 5, 1);

        $this->assertFalse(!$paginator->onFirstPage());
        $this->assertFalse($paginator->hasMorePages());
        $this->assertSame(1, $paginator->lastPage());
    }
}
