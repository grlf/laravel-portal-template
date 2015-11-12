<?php namespace App\Repos\Files;

use App\Repos\DbRepository;

class DbFileRepository implements FileRepositoryInterface {

    use DbRepository;

    public function __construct(File $model)
    {
        $this->model = $model;
    }

    public function deleteMultipleFiles($fileIds)
    {

        if ( File::destroy($fileIds) ) {
            return true;
        } else {
            return false;
        }

    }


}
