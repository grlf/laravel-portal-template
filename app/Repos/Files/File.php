<?php namespace App\Repos\Files;

use App\Repos\UserTrackModel;

class File extends UserTrackModel {

    protected $dates = [];

    protected $fillable = [

    ];


    /**
     * Gets the system path that all files for this model are stored on
     * Is used for storing files to the file system
     *
     * @return string
     */
    public function getSystemPath()
    {

        // define the system path, and make the path if it doesn't exist
        $fs_path = \Config::get('portal.file_system_path');
        \File::exists($fs_path) or \File::makeDirectory($fs_path);

        return $fs_path;
    }

    /**
     * Gets the url path that the browser loads the files from.
     * Is used for displaying in views
     *
     * @return string
     */
    public function getURLPath()
    {
        return \Config::get('portal.file_url_path');
    }

    /**
     * Gets the system path that all thumbnails for this model are stored on
     * Is used for storing thumbnails to the file system
     *
     * @return string
     */
    public function getThumbnailSystemPath()
    {

        $path = $this->getSystemPath() . 'thumbnails/';
        \File::exists($path) or \File::makeDirectory($path);

        return $path;
    }

    /**
     * Gets the url path that the browser loads the thumbnails from
     * Is used for displaying in views
     *
     * @return string
     */
    public function getThumbnailURLPath()
    {

        return $this->getURLPath() . 'thumbnails/';

    }

    public function fileable()
    {

        return $this->morphTo();
    }

    public function created_user()
    {
        return $this->hasOne('\App\User', 'id', 'created_by');
    }

}