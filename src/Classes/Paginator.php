<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Blossom\Classes;

class Paginator implements \Iterator, \ArrayAccess, \Countable
{
    public $items = [];
    public $itemsPerPage;
    public $currentPageNumber;
    public $totalItemCount    = 0;
    public $pageRange         = 10;

    /**
     * @param int $itemsPerPage
     * @param int $currentPageNumber
     */
    public function __construct($itemsPerPage=10, $currentPageNumber=1)
    {
        $this->items = [];
        $this->position = 0;

        $itemsPerPage      = (int)$itemsPerPage;
        $currentPageNumber = (int)$currentPageNumber;

        $this->itemsPerPage      = $itemsPerPage;
        $this->currentPageNumber = $currentPageNumber < 1 ? 1 : $currentPageNumber;
    }

    public function count() { return count($this->items); }

    public function offsetExists($offset) { return isset($this->items[$offset]); }
    public function offsetUnset ($offset) { unset($this->items[$offset]); }
    public function offsetGet   ($offset) { return $this->items[$offset]; }
    public function offsetSet   ($offset, $value) { $this->items[$offset] = $value; }

    public function rewind() { $this->position = 0; }
    public function current() { return $this->items[$this->position]; }
    public function key() { return $this->position; }
    public function next() { ++$this->position; }
    public function valid() { return isset($this->items[$this->position]); }


    /**
     * @return stdClass
     */
    public function getPages()
    {
        $pageRange  = $this->pageRange;
        $pageNumber = $this->currentPageNumber;
        $pageCount = (int) ceil($this->totalItemCount / $this->pageRange);

        if ($pageRange > $pageCount) {
            $pageRange = $pageCount;
        }

        $delta = ceil($pageRange / 2);

        if ($pageNumber - $delta > $pageCount - $pageRange) {
            $lowerBound = $pageCount - $pageRange + 1;
            $upperBound = $pageCount;
        }
        else {
            if ($pageNumber - $delta < 0) {
                $delta = $pageNumber;
            }

            $offset     = $pageNumber - $delta;
            $lowerBound = $offset + 1;
            $upperBound = $offset + $pageRange;
        }

        $pages = [];
        for ($pageNumber = $lowerBound; $pageNumber <= $upperBound; $pageNumber++) {
            $pages[$pageNumber] = $pageNumber;
        }

        $p = new \stdClass();
        $p->first   = 1;
        $p->last    = $pageCount;
        $p->current = $this->currentPageNumber;
        if ($this->currentPageNumber > 1)          { $p->previous = $this->currentPageNumber - 1; }
        if ($this->currentPageNumber < $pageCount) { $p->next     = $this->currentPageNumber + 1; }
        $p->pagesInRange = $pages;
        return $p;
    }
}