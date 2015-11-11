<?php

use App\Exceptions\OpsNotFoundException;
use App\Repos\Tags\Tag;
use App\User;
use Illuminate\Support\Facades\Cache;

/**
 * Link for sorting a grid
 *
 * @param $column
 * @param $body
 * @return string
 */
function sort_by($column, $body)
{
    $request = \Request::instance();

    $orig_sort = $request->get('dir');
    $orig_col = $request->get('sort');

    $direction = ($orig_sort == 'asc') ? 'desc' : 'asc';

    //Replace our current sort
    $request->merge(['sort' => $column, 'dir' => $direction]);
    $q_str = $request->query();

    //Reset the request to it's original state
    $request->merge(['sort' => $orig_col, 'dir' => $orig_sort]);

    return '<a href="/' . $request->path() . '/?' . http_build_query($q_str) . '">' . $body . '</a>';
}

/**
 * This method generates a link for filters
 *
 * @param $filters
 * @param $body
 * @return string
 */
function filter_by($filters, $body)
{
    $request = \Request::instance();

    //Check if currently selected
    $selected = true;
    $orig_filter = [];
    foreach ($filters as $key => $val) {
        $orig_filter[$key] = $request->query($key);
        if ( $request->query($key) != $val ) {
            $selected = false;
            break;
        }
    }
    //Replace our current filters
    unset($key, $val);
    if ( $filters ) {
        foreach ($filters as $key => $val) {
            $request->merge([$key => $val]);
        }
    }
    $q_str = $request->query();

    //Reset the request to it's original state
    unset($key, $val);
    foreach ($orig_filter as $key => $val) {
        $request->merge([$key => $val]);
    }
    if ( $selected ) {
        return $body;
    }
    return '<a href="/' . $request->path() . '/?' . http_build_query($q_str) . '">' . $body . '</a>';
}

/****
 * Clears the search results from the query string
 */
function clear_search()
{
    $request = \Request::instance();
    $q_str = $request->only(['pp', 'dir', 'sort', 'page']);

    return route('parts_path', $q_str);
}

/**
 * @param $certified
 * @return string
 */
function get_certified_icon($certified)
{
    if ( $certified ) {
        return '<i class="fa fa-flag green-icon" data-original-title="This part\'s revisions is ok."' .
        ' data-toggle="tooltip" ></i>';
    }

    return '<i class="fa fa-warning red-icon" data-original-title=' .
    '"This part has a revision waiting to be approved/acknowledged" data-toggle="tooltip"></i>';
}

/*
 * Returns top menu link formatted properly
 */
/**
 * @param $path_name
 * @param $lnk_txt
 * @param $fa_class
 * @return string
 */
function get_top_menu_link($path_name, $lnk_txt, $fa_class)
{
    $url = '';

    if ( $path_name === null ) {
        $url = '#';
    } else {
        $url = route($path_name);
    }
    $html = '<a href=' . $url . '><i class="fa ' . $fa_class . '"></i><span>' . $lnk_txt . '</span></a>';

    return $html;
}


/*
 * This function retuns a list of users
*/
function get_users_list()
{
    return Cache::remember('users.list', 60, function () {
        return User::orderBy('username')->select('username', 'first_name', 'last_name', 'id')
            ->get()->keyBy('id')->toArray();
    });
}


/**
 * This function will return a user by ID
 * @param $id
 * @return
 */
function get_user_by_id($id)
{
    return User::findOrFail($id);
}

/*
 * This function returns a list of tags
 */
function get_tags_list()
{
    return Cache::remember('tags.list', 60, function () {
        return Tag::orderBy('name')->lists('name', 'id');
    });
}

function get_pagination_options()
{
    return [10, 20, 50, 100];
}

/**
 * This function wildcards a string
 * @param $str
 * @return string
 */
function wildcard($str)
{
    return "%" . str_replace(" ", "%", $str) . "%";
}

/**
 * This function returns the standard date string format
 */
function standard_date_format()
{
    return 'n/d/Y h:i:s A';
}

/**
 * This function returns the abbreviations for the different
 * operation types
 *
 * @param $model
 * @return mixed
 * @throws OpsNotFoundException
 */
function get_op_abbr($model)
{
    $ops = Config::get('prodpro.operations');

    if ( isset($ops[$model]['abbr']) ) {
        return $ops[$model]['abbr'];
    }

    throw new OpsNotFoundException('Type ' . $model . ' not defined in abbr array');
}

/**
 * Takes a user array and formats it as first initial, last name
 *
 * @param $user
 * @return string
 */
function short_name($user)
{
    return substr(strtoupper($user['first_name']), 0, 1) . '. ' . ucwords($user['last_name']);
}

/**
 * Takes an array of users and returns them in an array with a
 * key => short_name relationship
 *
 * @param $users
 * @return array
 */
function short_name_array($users)
{
    uasort($users, 'user_last_name_sort');
    $arr = [];
    foreach ($users as $key => $user) {
        $arr[$key] = short_name($user);
    }
    return $arr;
}

/**
 * User sort for last name
 * @param $a
 * @param $b
 * @return int
 */
function user_last_name_sort($a, $b)
{
    if ( $a['last_name'] == $b['last_name'] ) {
        return 0;
    }
    return ($a['last_name'] < $b['last_name']) ? -1 : 1;
}

/**
 * Takes a user array and formats it as full name
 * @param $user
 * @return string
 */
function full_name($user)
{
    return ucwords($user['first_name']) . ' ' . ucwords($user['last_name']);
}

/*
 * This function prints all SQL queries to the screen
 */
function debug_sql()
{
    Event::listen('illuminate.query', function ($sql) {
        var_dump($sql);
    });
}