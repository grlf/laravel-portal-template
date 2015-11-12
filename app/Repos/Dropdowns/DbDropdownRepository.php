<?php namespace App\Repos\Dropdowns;

use App\Exceptions\IdDoesNotMatchModelException;
use App\Exceptions\ParentDoesNotExistException;
use App\Repos\DbRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class DbDropdownRepository implements DropdownRepositoryInterface {

    use DbRepository;

    /**
     * @param Dropdown $model
     */
    public function __construct(Dropdown $model)
    {
        $this->model = $model;
    }

    /**
     * This function takes a key and returns the dropdown associated with that model.
     * @param string $model
     * @param int $parent_id
     * @return mixed
     */
    public function getList($model, $parent_id = null)
    {
        $list = Cache::remember($this->getListCacheKey($model), 60, function () use ($model) {
            return $this->model->whereModel($model)->orderBy('order')
                ->orderBy('name')->get();
        });

        $items = [];
        $filtered = $list->each(function ($item) use ($parent_id, &$items) {
            if ( $item->parent_id == $parent_id || $parent_id === null ) {
                $items[$item->id] = $item->name;
            }
        });

        return $items;
    }

    /**
     * This function adds an item to a given dropdown model.
     * @param $request
     * @return static
     * @throws IdDoesNotMatchModelException
     * @throws ParentDoesNotExistException
     */
    public function addItem(Request $request)
    {
        $parent_id = $request->get('parent_id');

        if ( $parent_id > 0 ) {
            try {
                $parent = $this->model->findOrFail($parent_id);
            } catch(\Exception $e) {
                throw new ParentDoesNotExistException;
            }

        }

        $this->flushCache($request->get('model'));
        return $this->model->create($request->all());
    }


    /**
     * Returns the cache key based off of the model.
     * @param $key
     * @return string
     */
    protected function getListCacheKey($key)
    {
        return 'dd.list.' . $key;
    }


    /**
     * Return the text for a specific dropdown
     * @param $id
     * @param $model
     * @return string
     */
    public function getText($id, $model)
    {
        if ( empty($id) ) {
            return '';
        }

        $list = $this->getList($model);

        return $list[$id];
    }

    /**
     * @param Request $request
     * @throws IdDoesNotMatchModelException
     */
    public function deleteItem(Request $request)
    {
        //Check that id is within model
        $item = $this->model->findOrFail($request->get('id'));

        if ( $item->model != $request->get('model') ) {
            throw new IdDoesNotMatchModelException();
        }

        $item->delete();
        $this->flushCache($request->get('model'));
    }

    /**
     * @param Request $request
     */
    public function sort(Request $request)
    {
        //Set up our order array
        $order = [];
        foreach ($request->get('data') as $item) {
            $order[$item['sort_id']] = $item['order'] + 1;
        }

        //Get all the dropdowns
        $items = $this->getList($request->get('model'));

        //Update order
        unset($item);
        foreach ($items as $key => $item) {
            if ( isset($order[$key]) ) {
                $dd = Dropdown::findOrFail($key);
                $dd->order = $order[$key];
                $dd->save();
            }
        }
        $this->flushCache($request->get('model'));
    }

    /**
     * This function can be used to clear out the cache for a dropdown model
     * @param $model
     */
    public function flushCache($model)
    {
        Cache::forget($this->getListCacheKey($model));
    }
}
