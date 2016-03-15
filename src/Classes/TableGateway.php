<?php
/**
 * A base class that streamlines creation of ZF2 TableGateway
 *
 * @copyright 2014-16 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Blossom\Classes;

use Aura\SqlQuery\QueryFactory;

abstract class TableGateway
{
    protected $queryFactory;
    protected $tablename;
    protected $classname;

	/**
	 * @param string $table The name of the database table
	 * @param string $class The class model to use as a resultSetPrototype
	 *
	 * You must pass in the fully namespaced classname.  We do not assume
	 * any particular namespace for the models.
	 */
	public function __construct($table, $class)
	{
        $this->queryFactory = new QueryFactory(Database::getPlatform());
        $this->tablename = $table;
        $this->classname = $class;
	}

	/**
	 * Simple, default implementation for find
	 *
	 * This will allow you to do queries for rows in the table,
	 * where you provide field=>values for the where clause.
	 * Only fields actually in the table can be included this way.
	 *
	 * You generally want to override this implementation with your own
	 * However, this basic implementation will allow you to get up and
	 * running quicker.
	 *
	 * @param array $fields Key value pairs to select on
	 * @param array $order The default ordering to use for select
	 * @param int $itemsPerPage
	 * @param int $currentPage
	 */
	public function find($fields=null, $order=null, $itemsPerPage=null, $currentPage=null)
	{
        $select = $this->queryFactory->newSelect();
        $select->cols(['*'])->from($this->tablename);

		if (count($fields)) {
			foreach ($fields as $key=>$value) {
                if (isset($this->columns)) {
                    if (in_array($key, $this->columns)) {
                        $select->where("$key=?", $value);
                    }
                }
                else {
                    $select->where("$key=?", $value);
                }
			}
		}

        if ($order) { $select->orderBy($order); }
		return $this->performSelect($select, $itemsPerPage, $currentPage);
	}

	/**
	 * @param Aura\SqlQuery\Select $select
	 * @param Blossom\Classes\Paginator $paginator
	 * @return array An array of hydrated objects
	 */
	public function performSelect($select, $itemsPerPage=null, $currentPage=null)
	{
        $pdo = Database::getConnection();

        if ($itemsPerPage) {
            $currentPage = $currentPage ? $currentPage : 1;
            $paginator = new Paginator($itemsPerPage, $currentPage);

            $c = $this->queryFactory->newSelect();
            $c->cols(['count(*) as count'])
              ->fromSubSelect($select, 'o');
            $query = $pdo->prepare($c->getStatement());
            $query->execute($c->getBindValues());
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            $paginator->totalItemCount = $result[0]['count'];

            $select->limit ($paginator->itemsPerPage);
            $select->offset($paginator->itemsPerPage * ($paginator->currentPageNumber-1));

            $query = $pdo->prepare($select->getStatement());
            $query->execute($select->getBindValues());

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $class = $this->classname;
                $paginator->items[] = new $class($row);
            }
            return $paginator;
        }
        else {
            $query = $pdo->prepare($select->getStatement());
            $query->execute($select->getBindValues());

            $items = [];
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $class = $this->classname;
                $items[] = new $class($row);
            }
            return $result;
        }
	}
}
