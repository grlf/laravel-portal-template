<?php

namespace Tests\Traits;

use \Illuminate\Foundation\Testing\WithoutMiddleware;
use \Illuminate\Support\Facades\Cache;

trait APITrait
{
    use WithoutMiddleware;

    protected $api_header =  ['X-Requested-With' => "XMLHttpRequest"];

    /**
     * Checks that the sort is working in a standardized way
     * @param $url
     * @param $table
     * @param $model
     * @param array $options
     */
    protected function checkAPISort($url, $table, $model, $options = array())
    {
        //Add test items
        if ($model instanceof \Illuminate\Database\Eloquent\Collection) {
            $items = $model;
        } else {
            $items = factory($model, 10)->create();
            Cache::flush();
        }
        $items = $items->toArray();

        shuffle($items);
        $sort_data = [];
        foreach ($items as $key => $val) {
            $sort_data[] = ['order' => $key, 'sort_id' => $val['id']];
        }

        $request = ['data' => $sort_data];

        if ($options) {
            $request += $options;
        }

        //Call
        $this->patch($url, $request, $this->api_header);
        $this->assertResponseStatus(200);

        //Check for record order
        unset($key, $val);
        foreach ($sort_data as $key => $val) {
            $this->seeInDatabase($table, [
                'id' => $val['sort_id'],
                'order' => ($key + 1)
            ]);
        }
    }

    /**
     * This function checks that a given API url spits back
     * a list of items in the correct order.
     * @param $url
     */
    /*protected function checkAPIListGet($url, $model)
    {
        $items = factory($model, 4)->create();

        $this->get($url);
        $this->assertResponseOk();

        foreach ($items as $key => $item) {
            $this->seeJson([
                'id' => $item,
                'order' => ($key + 1),
            ]);
        }
    }*/

    /**
     * Checks that the API is listing objects in a standardized way
     * @param $url
     * @param $model - Class to run a factory on or a collection of items to test the list of
     * @param $context_id
     * @param $context_type
     * @param bool|false $order_check
     */
    protected function checkAPIListPost($url, $model, $context_id, $context_type, $order_check = false)
    {
        //Add test items
        if ($model instanceof \Illuminate\Database\Eloquent\Collection) {
            $items = $model;
        } else {
            $items = factory($model, 10)->create()->toArray();
            Cache::flush();
        }


        $this->post(
            $url,
            [
                'context_id' => $context_id,
                'context_type' => $context_type
            ],
            $this->api_header
        );

        $this->assertResponseStatus(200);

        foreach ($items as $key => $item) {
            $data['id'] = $item['id'];

            if ($order_check) {
                $data['order'] = ($key +1);
            }

            $this->seeJson($data);
        }
    }

    /**
     * Checks that the API is editing the object in a standardized way
     * @param $url - this URL SHOULD NOT include the id.  That will be appended by the function
     *         once it has added the data.
     * @param $model
     * @param array $edit_data
     * @param $table
     */
    protected function checkAPIEdit($url, $model, array $edit_data, $table)
    {
        //Add test item
        if ($model instanceof \Illuminate\Database\Eloquent\Model) {
            $item = $model;
        } else {
            $item = factory($model)->create();
            Cache::flush();
        }

        $this->patch(
            $url . '/' . $item->id,
            $edit_data,
            $this->api_header
        );
        $this->assertResponseStatus(200);

        //Remove any instances of context for checking
        unset($edit_data['context_id'],$edit_data['context_type']);

        $this->seeJson($edit_data);

        $edit_data['id'] = $item->id;
        $this->seeInDatabase(
            $table,
            $edit_data
        );
    }

    /**
     * This function checks that a deletion has occurred
     * @param $url - this URL SHOULD NOT include the id.  That will be appended
     * @param $model
     * @param $table
     * @param bool|true $softDelete - whether the model is set up for soft delete or not
     * @param null $sortable - - array ['url' => URL for list, 'context_id', 'context_type']
     */
    protected function checkAPIDelete($url, $model, $table, $softDelete = true, $sortable = null)
    {
        if ($model instanceof \Illuminate\Support\Collection) {
            $items = $model;
        } else {
            $items = factory($model, 10)->create()->toArray();
            Cache::flush();
        }
        $toDelete = 2;

        $this->login();
        $this->delete(
            $url . '/' . $items[$toDelete]['id'],
            [],
            $this->api_header
        );

        $this->assertResponseStatus(200);

        if ($softDelete) {
            //Make sure record is still there after delete
            $this->seeInDatabase(
                $table,
                [
                    'id' => $items[$toDelete]['id']
                ]
            );
        }
        $this->notSeeInDatabase(
            $table,
            [
                'id' => $items[$toDelete]['id'],
                'deleted_at' => null
            ]
        );

        if ($sortable) {
            $this->post(
                $sortable['url'],
                [
                    'context_id' => $sortable['context_id'],
                    'context_type' => $sortable['context_type']
                ],
                $this->api_header
            );

            $this->assertResponseOk();

            //Remove deleted id and reindex array
            unset($items[$toDelete]);
            $items = array_values($items);
            foreach ($items as $key => $item) {
                $this->seeJson(
                    [
                        'order' => ($key + 1)
                    ]
                );
            }
        }
    }

    protected function checkDeletes($url, $model, $table, $softDelete = true)
    {
        $this->delete(
            $url,
            [],
            $this->api_header
        );
        $this->assertResponseStatus(302);

        if ($softDelete) {
            //Make sure record is still there after delete
            $this->seeInDatabase(
                $table,
                [
                    'id' => $model->id
                ]
            );
        }
        $this->notSeeInDatabase(
            $table,
            [
                'id' => $model->id,
                'deleted_at' => null
            ]
        );
    }
}
