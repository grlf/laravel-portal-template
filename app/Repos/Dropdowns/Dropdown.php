<?php namespace App\Repos\Dropdowns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dropdown extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'model',
        'name',
        'order',
        'parent_id'
    ];
}
