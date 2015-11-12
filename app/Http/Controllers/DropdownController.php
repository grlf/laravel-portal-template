<?php

namespace App\Http\Controllers;

use App\Http\Requests\DropdownRequest;
use App\Repos\Dropdowns\DropdownRepositoryInterface;
use Illuminate\Http\Request;

class DropdownController extends Controller {

    protected $dropdown;

    /**
     * @param DropdownRepositoryInterface $dropdown
     */
    public function __construct(DropdownRepositoryInterface $dropdown)
    {
        $this->dropdown = $dropdown;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        //Validate the request
        $this->validate($request, [
            'model' => 'required'
        ]);

        $list = $this->dropdown->getList($request->get('model'), $request->get('parent_id', null));

        return response()->json($list, 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param DropdownRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DropdownRequest $request)
    {
        $code = $this->dropdown->addItem($request);

        return response()->json(['name' => $code->name, 'id' => $code->id], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        //Validate request
        $this->validate($request, [
            'id'    => 'required',
            'model' => 'required'
        ]);
        $this->dropdown->deleteItem($request);

        return response()->json(['msg' => 'Dropdown deleted.'], 200);
    }

    /**
     * Sorts the dropdowns
     * @param Request $request
     */
    public function sort(Request $request)
    {
        $this->validate($request, [
            'model' => 'required'
        ]);

        $this->dropdown->sort($request);
    }
}
