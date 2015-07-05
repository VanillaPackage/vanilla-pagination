<?php

namespace Rentalhost\VanillaPagination;

use PHPUnit_Framework_TestCase;

class PaginationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test basic methods.
     * @covers Rentalhost\VanillaPagination\Pagination::__construct
     * @covers Rentalhost\VanillaPagination\Pagination::getClamp
     * @covers Rentalhost\VanillaPagination\Pagination::getCurrentPage
     * @covers Rentalhost\VanillaPagination\Pagination::setCurrentPage
     * @covers Rentalhost\VanillaPagination\Pagination::getTotalItems
     * @covers Rentalhost\VanillaPagination\Pagination::getItemsPerPage
     * @covers Rentalhost\VanillaPagination\Pagination::getTotalPages
     * @covers Rentalhost\VanillaPagination\Pagination::getCurrentPageOffsets
     * @covers Rentalhost\VanillaPagination\Pagination::hasPage
     * @covers Rentalhost\VanillaPagination\Pagination::hasPages
     */
    public function testBasic()
    {
        $pagination = new Pagination(200, 20);

        // Initial values.
        $this->assertSame(1, $pagination->getCurrentPage());
        $this->assertSame(200, $pagination->getTotalItems());
        $this->assertSame(20, $pagination->getItemsPerPage());
        $this->assertSame(10, $pagination->getTotalPages());
        $this->assertTrue($pagination->hasPages());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(1, $pageOffsets->firstItem);
        $this->assertSame(20, $pageOffsets->lastItem);

        // Check if all pages exists, correctly.
        for ($i = 1; $i <= 10; $i++) {
            $this->assertTrue($pagination->hasPage($i));
        }

        // Advance to next page.
        $this->assertTrue($pagination->setCurrentPage(2));
        $this->assertSame(2, $pagination->getCurrentPage());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(21, $pageOffsets->firstItem);
        $this->assertSame(40, $pageOffsets->lastItem);

        // Advance to last page.
        $this->assertTrue($pagination->setCurrentPage(10));
        $this->assertSame(10, $pagination->getCurrentPage());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(181, $pageOffsets->firstItem);
        $this->assertSame(200, $pageOffsets->lastItem);

        // Attempts to navigate to a page beyond the first.
        $this->assertFalse($pagination->setCurrentPage(0));
        $this->assertSame(1, $pagination->getCurrentPage());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(1, $pageOffsets->firstItem);
        $this->assertSame(20, $pageOffsets->lastItem);

        // Attempts to navigate to a page beyond the last.
        $this->assertFalse($pagination->setCurrentPage(11));
        $this->assertSame(10, $pagination->getCurrentPage());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(181, $pageOffsets->firstItem);
        $this->assertSame(200, $pageOffsets->lastItem);
    }

    /**
     * Test recalculate.
     * @covers Rentalhost\VanillaPagination\Pagination::setCurrentPage
     * @covers Rentalhost\VanillaPagination\Pagination::setCurrentItem
     * @covers Rentalhost\VanillaPagination\Pagination::setTotalItems
     * @covers Rentalhost\VanillaPagination\Pagination::setItemsPerPage
     * @covers Rentalhost\VanillaPagination\Pagination::getCurrentPageOffsets
     */
    public function testRecalculate()
    {
        $pagination = new Pagination(200, 20);
        $pagination->setCurrentPage(1);

        $this->assertSame(1, $pagination->getCurrentPage());
        $this->assertSame(200, $pagination->getTotalItems());
        $this->assertSame(20, $pagination->getItemsPerPage());
        $this->assertSame(10, $pagination->getTotalPages());
        $this->assertTrue($pagination->hasPages());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(1, $pageOffsets->firstItem);
        $this->assertSame(20, $pageOffsets->lastItem);

        // Total items: 100.
        $pagination->setTotalItems(100);

        $this->assertSame(1, $pagination->getCurrentPage());
        $this->assertSame(100, $pagination->getTotalItems());
        $this->assertSame(20, $pagination->getItemsPerPage());
        $this->assertSame(5, $pagination->getTotalPages());
        $this->assertTrue($pagination->hasPages());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(1, $pageOffsets->firstItem);
        $this->assertSame(20, $pageOffsets->lastItem);

        // Total items: 0.
        $pagination->setTotalItems(0);

        $this->assertSame(0, $pagination->getCurrentPage());
        $this->assertSame(0, $pagination->getTotalItems());
        $this->assertSame(20, $pagination->getItemsPerPage());
        $this->assertSame(0, $pagination->getTotalPages());
        $this->assertFalse($pagination->hasPages());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(0, $pageOffsets->firstItem);
        $this->assertSame(0, $pageOffsets->lastItem);

        // Total items: 150.
        $pagination->setTotalItems(150);

        $this->assertSame(1, $pagination->getCurrentPage());
        $this->assertSame(150, $pagination->getTotalItems());
        $this->assertSame(20, $pagination->getItemsPerPage());
        $this->assertSame(8, $pagination->getTotalPages());
        $this->assertTrue($pagination->hasPages());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(1, $pageOffsets->firstItem);
        $this->assertSame(20, $pageOffsets->lastItem);

        // Items per page: 40.
        $pagination->setItemsPerPage(40);

        $this->assertSame(1, $pagination->getCurrentPage());
        $this->assertSame(150, $pagination->getTotalItems());
        $this->assertSame(40, $pagination->getItemsPerPage());
        $this->assertSame(4, $pagination->getTotalPages());
        $this->assertTrue($pagination->hasPages());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(1, $pageOffsets->firstItem);
        $this->assertSame(40, $pageOffsets->lastItem);

        // Current page: 5.
        $pagination->setCurrentPage(5);

        $this->assertSame(4, $pagination->getCurrentPage());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(121, $pageOffsets->firstItem);
        $this->assertSame(150, $pageOffsets->lastItem);

        // Items per page: 20.
        $pagination->setItemsPerPage(20);

        $this->assertSame(7, $pagination->getCurrentPage());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(121, $pageOffsets->firstItem);
        $this->assertSame(140, $pageOffsets->lastItem);

        // Total items: 0, Items per page: 50.
        $pagination->setTotalItems(0);
        $pagination->setItemsPerPage(50);

        $this->assertSame(0, $pagination->getCurrentPage());

        $pageOffsets = $pagination->getCurrentPageOffsets();
        $this->assertSame(0, $pageOffsets->firstItem);
        $this->assertSame(0, $pageOffsets->lastItem);
    }

    /**
     * Test getPagesIterator.
     * @covers Rentalhost\VanillaPagination\Pagination::getPagesIterator
     */
    public function testGetPagesIterator()
    {
        $pagination = new Pagination(200, 20);
        $this->assertSame([ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ], $pagination->getPagesIterator()->getArrayCopy());

        $pagination = new Pagination(199, 20);
        $this->assertSame([ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ], $pagination->getPagesIterator()->getArrayCopy());

        $pagination = new Pagination(181, 20);
        $this->assertSame([ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ], $pagination->getPagesIterator()->getArrayCopy());

        $pagination = new Pagination(1, 20);
        $this->assertSame([ 1 ], $pagination->getPagesIterator()->getArrayCopy());

        $pagination = new Pagination(20, 20);
        $this->assertSame([ 1 ], $pagination->getPagesIterator()->getArrayCopy());

        $pagination = new Pagination(0, 20);
        $this->assertSame([], $pagination->getPagesIterator()->getArrayCopy());
    }

    /**
     * Test getNearPagesIterator.
     * @covers Rentalhost\VanillaPagination\Pagination::getNearPagesIterator
     */
    public function testgetNearPagesIterator()
    {
        $pagination = new Pagination(0, 20);
        $this->assertSame([], $pagination->getNearPagesIterator(5)->getArrayCopy());

        $pagination = new Pagination(1, 20);
        $this->assertSame([ 1 ], $pagination->getNearPagesIterator(3)->getArrayCopy());

        $pagination = new Pagination(50, 20);

        $pagination->setCurrentPage(1);
        $this->assertSame([ 1, 2, 3 ], $pagination->getNearPagesIterator(3)->getArrayCopy());

        $pagination->setCurrentPage(2);
        $this->assertSame([ 1, 2, 3 ], $pagination->getNearPagesIterator(3)->getArrayCopy());

        $pagination->setCurrentPage(3);
        $this->assertSame([ 1, 2, 3 ], $pagination->getNearPagesIterator(3)->getArrayCopy());

        $pagination = new Pagination(60, 20);

        $pagination->setCurrentPage(1);
        $this->assertSame([ 1, 2, 3 ], $pagination->getNearPagesIterator(4)->getArrayCopy());

        $pagination->setCurrentPage(2);
        $this->assertSame([ 1, 2, 3 ], $pagination->getNearPagesIterator(4)->getArrayCopy());

        $pagination->setCurrentPage(3);
        $this->assertSame([ 1, 2, 3 ], $pagination->getNearPagesIterator(4)->getArrayCopy());

        $pagination = new Pagination(200, 20);

        $pagination->setCurrentPage(1);
        $this->assertSame([ 1, 2, 3, 4, 5, 6, 7 ], $pagination->getNearPagesIterator(7)->getArrayCopy());

        $pagination->setCurrentPage(2);
        $this->assertSame([ 1, 2, 3, 4, 5, 6, 7 ], $pagination->getNearPagesIterator(7)->getArrayCopy());

        $pagination->setCurrentPage(3);
        $this->assertSame([ 1, 2, 3, 4, 5, 6, 7 ], $pagination->getNearPagesIterator(7)->getArrayCopy());

        $pagination->setCurrentPage(4);
        $this->assertSame([ 1, 2, 3, 4, 5, 6, 7 ], $pagination->getNearPagesIterator(7)->getArrayCopy());

        $pagination->setCurrentPage(5);
        $this->assertSame([ 2, 3, 4, 5, 6, 7, 8 ], $pagination->getNearPagesIterator(7)->getArrayCopy());

        $pagination->setCurrentPage(6);
        $this->assertSame([ 3, 4, 5, 6, 7, 8, 9 ], $pagination->getNearPagesIterator(7)->getArrayCopy());

        $pagination->setCurrentPage(7);
        $this->assertSame([ 4, 5, 6, 7, 8, 9, 10 ], $pagination->getNearPagesIterator(7)->getArrayCopy());

        $pagination->setCurrentPage(8);
        $this->assertSame([ 4, 5, 6, 7, 8, 9, 10 ], $pagination->getNearPagesIterator(7)->getArrayCopy());

        $pagination->setCurrentPage(9);
        $this->assertSame([ 4, 5, 6, 7, 8, 9, 10 ], $pagination->getNearPagesIterator(7)->getArrayCopy());

        $pagination->setCurrentPage(10);
        $this->assertSame([ 4, 5, 6, 7, 8, 9, 10 ], $pagination->getNearPagesIterator(7)->getArrayCopy());

        $pagination->setCurrentPage(1);
        $this->assertSame([ 1, 2, 3, 4, 5, 6, 7, 8 ], $pagination->getNearPagesIterator(8)->getArrayCopy());

        $pagination->setCurrentPage(2);
        $this->assertSame([ 1, 2, 3, 4, 5, 6, 7, 8 ], $pagination->getNearPagesIterator(8)->getArrayCopy());

        $pagination->setCurrentPage(3);
        $this->assertSame([ 1, 2, 3, 4, 5, 6, 7, 8 ], $pagination->getNearPagesIterator(8)->getArrayCopy());

        $pagination->setCurrentPage(4);
        $this->assertSame([ 1, 2, 3, 4, 5, 6, 7, 8 ], $pagination->getNearPagesIterator(8)->getArrayCopy());

        $pagination->setCurrentPage(5);
        $this->assertSame([ 2, 3, 4, 5, 6, 7, 8, 9 ], $pagination->getNearPagesIterator(8)->getArrayCopy());

        $pagination->setCurrentPage(6);
        $this->assertSame([ 3, 4, 5, 6, 7, 8, 9, 10 ], $pagination->getNearPagesIterator(8)->getArrayCopy());

        $pagination->setCurrentPage(7);
        $this->assertSame([ 3, 4, 5, 6, 7, 8, 9, 10 ], $pagination->getNearPagesIterator(8)->getArrayCopy());

        $pagination->setCurrentPage(8);
        $this->assertSame([ 3, 4, 5, 6, 7, 8, 9, 10 ], $pagination->getNearPagesIterator(8)->getArrayCopy());

        $pagination->setCurrentPage(9);
        $this->assertSame([ 3, 4, 5, 6, 7, 8, 9, 10 ], $pagination->getNearPagesIterator(8)->getArrayCopy());

        $pagination->setCurrentPage(10);
        $this->assertSame([ 3, 4, 5, 6, 7, 8, 9, 10 ], $pagination->getNearPagesIterator(8)->getArrayCopy());
    }
}
