<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestCase extends \Illuminate\Foundation\Testing\TestCase {

    use DatabaseTransactions;

    /**
     * @before
     */
    public function migrate()
    {
        \Artisan::call('migrate:refresh');

    }

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function check_authorized_route($method, $uri)
    {
        //Not logged in
        $response = $this->call($method, $uri);
        $this->assertRedirectedTo('/');

        //Logged in
        $this->login();
        $response = $this->call($method, $uri);
        $this->assertEquals(200, $response->getStatusCode());
    }

    protected function login()
    {
        //Login as our test user
        $user = User::findOrFail(1);
        $this->be($user);

        return $user;
    }

    /**
     * This function gets a random dropdown from provided model
     * @param $model
     * @return mixed
     */
    protected function randomDD($model)
    {
        $prodProData = new ProdProData();
        $prodProData->getList($model);

        return Dropdown::whereModel($model)->get()->random();
    }

    /**
     * Get a random user
     *
     * @return mixed
     */
    protected function randomUser()
    {
        return User::all()->random();
    }

    /**
     * @param $response
     * @param $key
     * @return mixed
     */
    protected function getResponseData($response, $key)
    {
        $content = $response->getOriginalContent()->getData();

        return $content[$key];
    }

    /**
     * The assert is only able to check alpha numeric consistently.
     * This test will be skipped if either of the strings start with a non-alphanumeric character
     * @param $table_class
     * @param $index
     * @param array $options
     */
    protected function checkTableSort($table_class, $index, $options = array())
    {
        //Sort ASC
        $comp_rows = $this->clickSort($table_class, $index, $options);
        if ( ctype_alnum(substr($comp_rows[0], 0, 1)) && ctype_alnum(substr($comp_rows[1], 0, 1)) ) {
            $this->assertGreaterThanOrEqual($comp_rows[0], $comp_rows[1]);
        }

        //Sort DESC
        $comp_rows = $this->clickSort($table_class, $index, $options);
        if ( ctype_alnum(substr($comp_rows[0], 0, 1)) && ctype_alnum(substr($comp_rows[1], 0, 1)) ) {
            $this->assertLessThanOrEqual($comp_rows[0], $comp_rows[1]);
        }
    }


    /**
     * Clicks the sort link on the table passed and returns the first two results
     *
     * @param $table_class
     * @param $index
     * @param array $options
     * @return array
     */
    private function clickSort($table_class, $index, $options = array())
    {
        $link = $this->crawler->filterXPath('//table[contains(@class,"' . $table_class . '")]/thead/tr/th[' . $index .
            ']/a')->link();
        $this->visit($link->getUri());

        $rows = $this->crawler->filterXPath('//table[contains(@class,"' . $table_class . '")]/tbody/tr/td[' . $index .
            ']');
        $row1 = $rows->eq(0)->text();
        $row2 = $rows->eq(1)->text();

        if ( !empty($options['date']) ) {
            if ( $options['date'] == 'm/d/Y' ) {
                $pattern = '/\d+\/\d+\/\d+/';
                if ( preg_match($pattern, $row1, $match) ) {
                    $row1 = date_format(date_create_from_format('m/d/Y', $match[0]), 'U');
                }

                if ( preg_match($pattern, $row2, $match) ) {
                    $row2 = date_format(date_create_from_format('m/d/Y', $match[0]), 'U');
                }
            }
        }

        return [strtolower($row1), strtolower($row2)];
    }

    /**
     * Returns the CSRF Token
     * @return null|string
     */
    protected function getCsrfToken()
    {
        return $this->crawler->filter("meta[name='_token']")->eq(0)->attr('content');
    }

}
