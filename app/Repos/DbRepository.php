<?php

namespace App\Repos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait DbRepository {

    /**
     * The base eloquent model
     * @var Model
     */
    protected $model;

    /**
     * The current sort field and direction
     * @var array
     */
    protected $currentSort = array('created_at', 'desc');

    /**
     * The current number of results to return per page
     * @var integer
     */
    protected $perPage = 25;

    /**
     * The relationships to eager load
     */
    protected $_eager = [];

    /**
     * Creates a new model
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Sets the number of items displayed per page of results
     * @param integer $perPage The number of items to display per page
     * @return DbRepository The current instance
     */
    public function setPaginate($perPage)
    {
        $this->perPage = (int)$perPage;

        return $this;
    }

    /**
     * Sets how the results are sorted
     * @param string $field The field being sorted
     * @param string $direction The direction to sort (ASC or DESC)
     * @return DbRepository The current instance
     */
    public function sortBy($field, $direction = 'DESC')
    {
        $direction = (strtoupper($direction) == 'ASC') ? 'ASC' : 'DESC';
        $this->currentSort = array($field, $direction);

        return $this;
    }

    /**
     * Sets the models that are eager loaded
     */
    public function setEager(array $models)
    {
        $this->_eager = array_unique(array_merge($this->_eager, $models));
    }

    /**
     * Creates a new QueryBuilder instance and applies the current sorting
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query()
    {
        list($sortField, $sortDir) = $this->currentSort;
        return $this->model->newQuery()->orderBy($sortField, $sortDir);
    }

    /**
     * Retrieves a set of items based on a single value
     * @param string $fieldName The name of the field to match
     * @param string $fieldValue The value of the field to match
     * @return \Illuminate\Pagination\Paginator | \Illuminate\Support\Collection
     */
    public function getByField($fieldName, $fieldValue)
    {
        $query = $this->query()->where($fieldName, $fieldValue);

        return $this->getResults($query);
    }

    public function find($id)
    {
        $query = $this->query();

        if ( !empty($this->_eager) ) {
            $query->with($this->_eager);
        }
        return $query->findOrFail($id);
    }

    /**
     * Retrieves as set of all items
     *
     */
    public function getAll()
    {
        $this->perPage = 0;
        return $this->getResults($this->query());
    }

    public function getAllList($textKey)
    {

        return $this->model->lists($textKey, 'id');

    }

    public function getPaginated()
    {
        return $this->getResults($this->query());
    }

    protected function getResults($query)
    {
        //See if we need to eager load
        if ( !empty($this->_eager) ) {
            $query->with($this->_eager);
        }

        return ($this->perPage > 0) ? $query->paginate($this->perPage) : $query->get();
    }

    /**
     * Sets the conditions for the sort based off the request
     *
     * @param Request $request
     */
    public function setSortFromRequest(Request $request)
    {
        $sort = $request->get('sort');
        $dir = $request->get('dir');

        //See if we're sorting
        if ( !empty($sort) && !empty($dir) ) {
            $this->sortBy($sort, $dir);
        }
    }

}