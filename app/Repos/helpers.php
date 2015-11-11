<?php

use App\User;
use App\Repos\Tags\Tag;
use App\Exceptions\OpsNotFoundException;
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
    $request->merge(['sort' => $column, 'dir' =>$direction]);
    $q_str = $request->query();

    //Reset the request to it's original state
    $request->merge(['sort' => $orig_col, 'dir' => $orig_sort]);

    return '<a href="/' . $request->path() . '/?' . http_build_query($q_str) .'">' . $body . '</a>';
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
        if ($request->query($key) != $val) {
            $selected = false;
            break;
        }
    }
    //Replace our current filters
    unset($key,$val);
    if ($filters) {
        foreach ($filters as $key => $val) {
            $request->merge([$key => $val]);
        }
    }
    $q_str = $request->query();

    //Reset the request to it's original state
    unset($key,$val);
    foreach ($orig_filter as $key => $val) {
        $request->merge([$key => $val]);
    }
    if ($selected) {
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
    if ($certified) {
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

    if ($path_name === null) {
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
    return [10,20,50,100];
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

    if (isset($ops[$model]['abbr'])) {
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
    if ($a['last_name'] == $b['last_name']) {
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

define('PART_IMG', 'data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAABVAAD/4QNvaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjAtYzA2MCA2MS4xMzQ3NzcsIDIwMTAvMDIvMTItMTc6MzI6MDAgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ9InhtcC5kaWQ6MDE4MDExNzQwNzIwNjgxMThEQkJEMTJGMzRCNTFGOEMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QzVGQUNCRTZBMjYyMTFFNEEyMkQ4RDA3MTMwQzc4NzEiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QzVGQUNCRTVBMjYyMTFFNEEyMkQ4RDA3MTMwQzc4NzEiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBNYWNpbnRvc2giPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDowMjgwMTE3NDA3MjA2ODExOERCQkQxMkYzNEI1MUY4QyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDowMTgwMTE3NDA3MjA2ODExOERCQkQxMkYzNEI1MUY4QyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6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');
