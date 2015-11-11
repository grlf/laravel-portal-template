<?php namespace App\Repos;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Repos\UserTrackModel
 *
 */
class UserTrackModel extends Model {


    public static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            if ( !$model->isDirty('updated_by') ) {
                $model->updated_by = \Auth::user()->id;
            }

        });

        static::creating(function ($model) {
            if ( !$model->isDirty('updated_by') ) {
                $model->updated_by = \Auth::user()->id;
            }

            if ( !$model->isDirty('created_by') ) {
                $model->created_by = \Auth::user()->id;
            }

        });
    }

}