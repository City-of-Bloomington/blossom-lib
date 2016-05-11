<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Blossom\Classes;

class SolrPaginator extends Paginator
{
	private $solrObject;

	public function __construct(\Apache_Solr_Response $solrObject, $itemsPerPage=10, $currentPageNumber=1)
	{
        parent::__construct($itemsPerPage, $currentPageNumber);

		$this->solrObject     = $solrObject;
		$this->totalItemCount = $this->solrObject->response->numFound;
		$this->items          = $this->solrObject->response->docs;
	}

	public function count()
	{
        return $this->totalItemCount;
	}
}
