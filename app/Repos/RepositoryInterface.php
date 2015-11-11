<?php namespace App\Repos;

use Illuminate\Http\Request;

interface RepositoryInterface {

    public function getAll();

    public function create(array $attributes);

    public function setPaginate($perPage);

    public function sortBy($sort, $dir);

    public function find($id);

    public function getPaginated();

    public function setSortFromRequest(Request $request);

}
