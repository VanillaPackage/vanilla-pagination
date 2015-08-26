<?php

namespace Rentalhost\VanillaPagination;

use ArrayIterator;

class PaginationOffset
{
    /**
     * Stores the first item offset.
     * @var integer
     */
    public $firstItem;

    /**
     * Stores the last item offset.
     * @var integer
     */
    public $lastItem;

    /**
     * Construct the PaginationOffset instance.
     *
     * @param integer $firstItem First item offset.
     * @param integer $lastItem  Last item offset.
     */
    public function __construct($firstItem, $lastItem)
    {
        $this->firstItem = $firstItem;
        $this->lastItem = $lastItem;
    }

    /**
     * Returns a iterator with all items offset of current page.
     * @return ArrayIterator
     */
    public function getIterator()
    {
        if ($this->firstItem === 0 &&
            $this->lastItem === 0
        ) {
            return new ArrayIterator;
        }

        return new ArrayIterator(range($this->firstItem, $this->lastItem));
    }

    /**
     * Returns the number of items on offset.
     * @return integer
     */
    public function countItems()
    {
        if ($this->firstItem === 0 &&
            $this->lastItem === 0
        ) {
            return 0;
        }

        return $this->lastItem - $this->firstItem + 1;
    }
}
