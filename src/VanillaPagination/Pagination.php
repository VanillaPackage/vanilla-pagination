<?php

namespace Rentalhost\VanillaPagination;

use ArrayIterator;

class Pagination
{
    /**
     * Stores current page.
     * @var integer
     */
    private $currentPage;

    /**
     * Stores total items.
     * @var integer
     */
    private $totalItems;

    /**
     * Stores how much items per page.
     * @var integer
     */
    private $itemsPerPage;

    /**
     * Construct a Pagination.
     *
     * @param integer $totalItems   Total items.
     * @param integer $itemsPerPage Items per page.
     */
    public function __construct($totalItems, $itemsPerPage)
    {
        $this->currentPage = 1;
        $this->itemsPerPage = $itemsPerPage;

        $this->setTotalItems($totalItems);
    }

    /**
     * Return the value fitted on min and mad values.
     *
     * @param  integer $value Current value.
     * @param  integer $min   Min value.
     * @param  integer $max   Max value.
     *
     * @return integer
     */
    private static function getClamp($value, $min, $max)
    {
        return min($max, max($min, $value));
    }

    /**
     * Set current page.
     * Will return false if the new page is out of range, and was fitted.
     *
     * @param integer $currentPage Current page.
     *
     * @return boolean
     */
    public function setCurrentPage($currentPage)
    {
        if (!$this->hasPages()) {
            $this->currentPage = 0;

            return false;
        }

        $currentPage = (int) $currentPage;
        $this->currentPage = self::getClamp($currentPage, 1, $this->getTotalPages());

        return $currentPage === $this->currentPage;
    }

    /**
     * Set current item.
     * Current page will be calculated over it.
     * Will return false if the item offset is out of range, and was fitted.
     *
     * @param integer $currentItem Current item index.
     *
     * @return boolean
     */
    public function setCurrentItem($currentItem)
    {
        $currentItem = self::getClamp($currentItem, 1, $this->totalItems);
        $this->currentPage = (int) ( floor(( $currentItem - 1 ) / $this->itemsPerPage) ) + 1;
    }

    /**
     * Get current page.
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Returns the pagination offsets.
     * @return PaginationOffset
     */
    public function getCurrentPageOffsets()
    {
        if (!$this->hasPages()) {
            return new PaginationOffset(0, 0);
        }

        return new PaginationOffset(
            ( $this->currentPage - 1 ) * $this->itemsPerPage + 1,
            min($this->currentPage * $this->itemsPerPage, $this->totalItems)
        );
    }

    /**
     * Redefine the total items.
     *
     * @param integer $totalItems Total items.
     */
    public function setTotalItems($totalItems)
    {
        $this->totalItems = $totalItems;

        // Update current page.
        $this->setCurrentPage($this->currentPage);
    }

    /**
     * Get total items.
     * @return integer
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

    /**
     * Redefine the items per page.
     *
     * @param integer $itemsPerPage Items per page.
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $currentPageOffsets = $this->getCurrentPageOffsets();
        $this->itemsPerPage = $itemsPerPage;

        $this->setCurrentItem($currentPageOffsets->firstItem);
    }

    /**
     * Get items per page.
     * @return integer
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * Get number of pages.
     * @return integer
     */
    public function getTotalPages()
    {
        return (int) ceil($this->totalItems / $this->itemsPerPage);
    }

    /**
     * Get an iterator with all pages.
     * @return ArrayIterator
     */
    public function getPagesIterator()
    {
        if (!$this->hasPages()) {
            // Empty pages.
            return new ArrayIterator;
        }

        return new ArrayIterator(range(1, $this->getTotalPages()));
    }

    /**
     * Get an iterator with pages near to current page.
     *
     * @param  integer $pagesCount Number of pages to return.
     *
     * @return ArrayIterator
     */
    public function getNearPagesIterator($pagesCount)
    {
        if (!$this->hasPages()) {
            // Empty pages.
            return new ArrayIterator;
        }

        // Return all pages if requested pages is over total pages.
        $totalPages = $this->getTotalPages();
        if ($pagesCount >= $totalPages) {
            return $this->getPagesIterator();
        }

        // Get the middle of pages count (discount the "current page").
        $pagesCountMiddle = ( $pagesCount - 1 ) / 2;

        // Calculates the most left page and the most right page.
        $pageLeft = $this->currentPage - (int) floor($pagesCountMiddle);
        $pageRight = $this->currentPage + (int) ceil($pagesCountMiddle);

        if ($pageLeft < 1) {
            // If most left page is less than one, so reset it and add the excess to most right page.
            $pageRight += abs($pageLeft) + 1;
            $pageLeft = 1;
        }
        elseif ($pageRight > $totalPages) {
            // If most right page is greater than total pages, so limit it to total pages
            // and reduce the excess on most left page.
            $pageLeft -= $pageRight - $totalPages;
            $pageRight = $totalPages;
        }

        return new ArrayIterator(range($pageLeft, $pageRight));
    }

    /**
     * Get an iterator with inner pages.
     *
     * It'll generate a pagination that always include the first and the last page, the center pages will work
     * similar to self::getNearPagesIterator() function, but will be generated between two spacers.
     *
     * @param integer    $pagesCount Number of pages to return (minimum 7).
     * @param mixed|null $spacer     Spacer to use on side of inner pages.
     *
     * @return ArrayIterator
     */
    public function getInnerPagesIterator($pagesCount, $spacer = null)
    {
        if (!$this->hasPages()) {
            // Empty pages.
            return new ArrayIterator;
        }

        // Consider minimum of 7 pages (first, last, two spacers, two side pages and current page).
        // Example: [ 1, ..., 5, <6>, 7, ..., 10 ]
        $pagesCount = max(7, $pagesCount);

        // Return all pages if requested pages is over total pages.
        $totalPages = $this->getTotalPages();
        if ($pagesCount >= $totalPages) {
            return $this->getPagesIterator();
        }

        // Determines the middle of pagination.
        $pagesMiddle = $pagesCount / 2;
        $pagesMiddleStart = (int) ceil($pagesMiddle);

        // If current page is lower or equal than middle-start, returns left complete pages.
        if ($this->currentPage <= $pagesMiddleStart) {
            return new ArrayIterator(array_merge(
                range(1, $pagesCount - $pagesMiddleStart + (int) ceil($pagesMiddle - 2)),
                [ $spacer, $this->getTotalPages() ]
            ));
        }

        // If current page is greater or equal than middle-end, returns right complete pages.
        $pagesMiddleEnd = $this->getTotalPages() - (int) ( $pagesCount - ceil($pagesMiddle) );
        if ($this->currentPage >= $pagesMiddleEnd) {
            return new ArrayIterator(array_merge(
                [ 1, $spacer ],
                range($pagesMiddleEnd - (int) ceil($pagesMiddle - 3), $this->getTotalPages())
            ));
        }

        // Calculates the most left page and the most right page.
        $pageLeft = $this->currentPage - (int) ceil($pagesMiddle - 3);
        $pageRight = $this->currentPage + (int) floor($pagesMiddle - 2);

        return new ArrayIterator(array_merge(
            [ 1, $spacer ],
            range($pageLeft, $pageRight),
            [ $spacer, $this->getTotalPages() ]
        ));
    }

    /**
     * Returns true if page exists.
     *
     * @param $page
     *
     * @return bool
     */
    public function hasPage($page)
    {
        return $page >= 1 &&
               $page <= $this->getTotalPages();
    }

    /**
     * Returns true if have some page to print (one or more).
     * @return boolean
     */
    public function hasPages()
    {
        return $this->getTotalPages() !== 0;
    }
}
