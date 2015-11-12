<?php

use Tests\Traits\APITrait;
use Tests\TestCase;

class DropdownAPITest extends TestCase
{
    use APITrait;

    private $parent_dd = [
        'model' => 'test.main',
        'name' => 'Master Test Dropdown'
    ];

    //dd($this->response->getContent());

    /**
     * @test
     */
    public function it_can_add_a_dropdown()
    {
        //check validation
        $this->post(
            '/api/v1/dd',
            [
                'name' => null
            ],
            $this->api_header
        );

        $this->assertResponseStatus(422);
        $this->seeJson([
            'name' => ['The name field is required.'],
            'model' => ['The model field is required.']
        ]);

        //Check insert
        $this->post(
            '/api/v1/dd',
            $this->parent_dd,
            $this->api_header
        );

        $this->seeJson([
            'name' => 'Master Test Dropdown'
        ]);

        $this->seeInDatabase(
            'dropdowns',
            [
                'model' => $this->parent_dd['model'],
                'name' => $this->parent_dd['name']
            ]
        );
    }

    /**
     * @test
     */
    public function it_can_delete_a_dropdown()
    {
        //Create a dropdown
        $dd = factory(App\Repos\Dropdowns\Dropdown::class)->create($this->parent_dd);

        //Check validation
        $this->delete(
            '/api/v1/dd',
            [],
            $this->api_header
        );

        $this->assertResponseStatus(422);
        $this->seeJson([
            'id' => ['The id field is required.'],
            'model' => ['The model field is required.']
        ]);

        //Check delete
        $this->delete(
            '/api/v1/dd',
            [
                'id' => $dd->id,
                'model' => $dd->model
            ],
            $this->api_header
        );

        $this->seeJson([
            'msg' => 'Dropdown deleted.'
        ]);

        //Check for soft delete
        $this->notSeeInDatabase('dropdowns', [
            'id' => $dd->id,
            'deleted_at' => null
        ]);
    }

    /**
     * @test
     */
    public function a_dropdown_can_only_be_deleted_if_its_id_is_in_the_model()
    {
        //Create a dropdown
        $dd = factory(App\Repos\Dropdowns\Dropdown::class)->create($this->parent_dd);

        //Check delete
        $this->delete(
            '/api/v1/dd',
            [
                'id' => $dd->id,
                'model' => 'no.good.model'
            ],
            $this->api_header
        );

        $this->seeStatusCode(500);
        $this->seeInDatabase('dropdowns', [
            'id' => $dd->id,
            'deleted_at' => null
        ]);
    }

    /**
     * @test
     */
    public function it_can_add_a_child_to_a_parent()
    {
        $parent = factory(App\Repos\Dropdowns\Dropdown::class)->create($this->parent_dd);

        $this->post(
            '/api/v1/dd',
            [
                'name' => "Test Child Dropdown",
                'model' => $parent->model,
                'parent_id' => $parent->id
            ],
            $this->api_header
        );

        $this->seeJson([
            'name' => 'Test Child Dropdown'
        ]);

        $child_id = json_decode($this->response->getContent())->id;
        $this->seeInDatabase('dropdowns', [
            'id' => $child_id,
            'name' => 'Test Child Dropdown',
            'parent_id' => $parent->id,
            'model' => $parent->model
        ]);
    }

    /**
     * @test
     */
    public function it_throws_an_error_when_the_parent_doesnt_exist()
    {
        $this->post('api/v1/dd',
            [
                'name' => "Test Child Dropdown",
                'model' => $this->parent_dd['model'],
                'parent_id' => 9999
            ],
            $this->api_header
        );

        $this->assertResponseStatus(500);
        $this->notSeeInDatabase('dropdowns',
            [
                'name' => "Test Child Dropdown"
            ]
        );
    }


    /**
     * @test
     */
    public function it_can_list_the_dropdowns_associated_with_a_model()
    {
        $this->post('api/v1/dd/list',
            [],
            $this->api_header
        );
        $this->assertResponseStatus(422);
        $this->seeJson([
            'model' => ['The model field is required.']
        ]);

        $temp = factory(App\Repos\Dropdowns\Dropdown::class, 5)->create();

        $this->post('api/v1/dd/list',
            ['model' => 'test.test'],
            $this->api_header
        );

        $this->assertResponseStatus(200);
        $list = json_decode($this->response->getContent(), true);

        $this->assertCount(5, $list);
    }

    /**
     * @test
     */
    public function it_can_list_out_the_children_dropowns()
    {
        $parent = factory(App\Repos\Dropdowns\Dropdown::class)->create();

        factory(App\Repos\Dropdowns\Dropdown::class, 3)->create([
            'parent_id' => $parent->id,
            'model' => 'test.2'
        ]);

        $this->post('api/v1/dd/list',
            [
                'model' => 'test.2',
                'parent_id' => $parent->id
            ],
            $this->api_header
        );

        $this->assertResponseStatus(200);
        $list = json_decode($this->response->getContent(), true);

        $this->assertCount(3, $list);
    }

    /**
     * There was a bug where if a model had parent_id set and you listed without any parent
     * it wouldn't bring back the full model
     * @test
     */
    public function it_can_list_all_models_regardless_of_parent()
    {
        $dd = factory(App\Repos\Dropdowns\Dropdown::class)->create(
            [
                'parent_id' => 99,
                'model' => 'new.test.model'
            ]
        );

        $this->post('api/v1/dd/list',
            [
                'model' => 'new.test.model'
            ],
            $this->api_header
        );

        $result = json_decode($this->response->getContent(), true);

        $this->assertCount(1, $result);
    }

    /**
     * @test
     */
    public function it_can_sort_the_dropdowns_of_a_model()
    {
        //Error check
        $this->patch('api/v1/dd/sort',
            [],
            $this->api_header
        );
        $this->assertResponseStatus(422);
        $this->seeJson([
            'model' => ['The model field is required.']
        ]);

        //Check that the sort works right
        $this->checkAPISort('api/v1/dd/sort', 'dropdowns', App\Repos\Dropdowns\Dropdown::class, ['model' => 'test.test']);

        //Check that the list comes back sorted
        $this->post('api/v1/dd/list',
            ['model' => 'test.test'],
            $this->api_header
        );
        $items = json_decode($this->response->getContent(), true);
        $count = 1;
        foreach ($items as $id => $val) {
            $this->seeInDatabase('dropdowns', [
                'id' => $id,
                'order' => $count
            ]);
            $count++;
        }
    }

    /**
     * @test
     */
    public function it_throws_an_error_when_you_sort_and_the_ids_dont_match_the_model()
    {
        $dd = factory(App\Repos\Dropdowns\Dropdown::class)->create();

        $this->patch('api/v1/dd/sort',
            [
                'data' => [
                    'sort_id' => $dd->id,
                    'order' => 99
                ],
                'model' => 'no.good.model'
            ],
            $this->api_header
        );

        $this->notSeeInDatabase('dropdowns',
            [
                'id' => $dd->id,
                'order' => 99
            ]);
    }
}
