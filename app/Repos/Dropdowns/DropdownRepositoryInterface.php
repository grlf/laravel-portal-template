<?php namespace App\Repos\Dropdowns;

use App\Repos\RepositoryInterface;
use Illuminate\Support\Facades\Request;

interface DropdownRepositoryInterface extends RepositoryInterface {

    public function getList($key);

    public function addItem($request);

    public function deleteItem($request);

    public function sort($request);
}
