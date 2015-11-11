<?php namespace App\Repos\Dropdowns;

use App\Repos\RepositoryInterface;
use Illuminate\Support\Facades\Request;

interface DropdownRepositoryInterface extends RepositoryInterface {

    public function getList($key);

    public function addItem(Request $request);

    public function deleteItem(Request $request);

    public function sort(Request $request);
}
