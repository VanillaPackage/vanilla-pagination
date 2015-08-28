<?php

namespace Rentalhost\VanillaPagination;

use PHPUnit_Framework_TestCase;

/**
 * Class PaginationOffsetTest
 * @package Rentalhost\VanillaPagination
 */
class PaginationOffsetTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test basic methods.
     * @covers Rentalhost\VanillaPagination\PaginationOffset::__construct
     * @covers Rentalhost\VanillaPagination\PaginationOffset::countItems
     */
    public function testBasic()
    {
        $paginationOffset = new PaginationOffset(0, 0);
        static::assertSame(0, $paginationOffset->countItems());

        $paginationOffset = new PaginationOffset(1, 1);
        static::assertSame(1, $paginationOffset->countItems());

        $paginationOffset = new PaginationOffset(1, 20);
        static::assertSame(20, $paginationOffset->countItems());
    }

    /**
     * Test getIterator.
     * @covers Rentalhost\VanillaPagination\PaginationOffset::getIterator
     */
    public function testGetIterator()
    {
        $paginationOffset = new PaginationOffset(0, 0);
        static::assertSame([ ], $paginationOffset->getIterator()->getArrayCopy());

        $paginationOffset = new PaginationOffset(1, 1);
        static::assertSame([ 1 ], $paginationOffset->getIterator()->getArrayCopy());

        $paginationOffset = new PaginationOffset(1, 5);
        static::assertSame([ 1, 2, 3, 4, 5 ], $paginationOffset->getIterator()->getArrayCopy());
    }
}
