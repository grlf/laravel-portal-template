<?php

namespace App\Http\Utilities;


use App\Repos\Files\File;

class StoreFileablefiles {


    /**
     * @var integer $fileable_id
     */
    protected $fileable_id;

    /**
     * @var string $fileable_type
     */
    protected $fileable_type;

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    protected $file;

    /**
     * @var Thumbnail $thumbnail
     */
    protected $thumbnail;

    public function __construct($fileable_type, $fileable_id, $file, $thumbnail)
    {

        $this->fileable_type = $fileable_type;
        $this->fileable_id = $fileable_id;
        $this->file = $file;
        $this->thumbnail = $thumbnail;
        $this->timestamp = time();
    }

    /**
     * Save the file to the fileable type and id
     */
    public function save()
    {

        $file = $this->makeFileRecord();

        // move the file and add the size to the file model
        $this->file->move($file->getSystemPath(), $file->filename);

        if ( \File::exists($file->getSystemPath() . $file->filename) ) {
            $file->filesize = \File::size($file->getSystemPath() . $file->filename);
        }

        if ( $this->thumbnail->isPhoto($file->getSystemPath() . $file->filename) ) {
            $this->thumbnail->make($file);
        }


        $file->save();
    }


    /**
     * Create a base file model class
     *
     * @return File
     */
    protected function makeFileRecord()
    {

        $file = new File();
        $file->fileable_type = $this->fileable_type;
        $file->fileable_id = $this->fileable_id;
        $file->filename = $this->timestamp . '-' . $this->file->getClientOriginalName();
        $file->filetype = $this->file->getClientMimeType();
        $file->filepath = $file->getURLPath() . $file->filename;
        $file->order = 0;

        return $file;
    }

    /**
     * Generates a thumbnail for image file types
     *
     * @param File $file
     */
    public function generateThumbnail($file)
    {

        // get and make sure the thumbnail system path is created
        $thumbnail_system_path = $file->getThumbnailSystemPath();
        $thumbnail_url_path = $file->getThumbnailURLPath();
        $thumbnail_name = '170-' . $file->filename;

        \Image::make($file->getSystemPath() . $file->filename)
            ->resize(170, null, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($thumbnail_system_path . $thumbnail_name);
        $file->thumbnail = $thumbnail_url_path . $thumbnail_name;

    }


}