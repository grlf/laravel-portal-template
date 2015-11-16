<?php

namespace App\Http\Utilities;

use Mockery as m;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\Cases\TestCase;


class FilesFunctionalTest extends TestCase {

    /**
     * @test
     */
    public function it_can_add_image_file_for_general()
    {
        $this->login();
        $this->image_for_item('App\Repos\General\General', 1);
    }

    /**
     * @test
     */
    public function it_can_add_non_image_file_for_general()
    {
        $this->login();
        $this->non_image_for_item('App\Repos\General\General', 1);
    }


    public function image_for_item($fileable_type, $fileable_id)
    {

        $fsPath = new \App\Repos\Files\File();
        $fsPath = $fsPath->getSystemPath();
        $file = m::mock(UploadedFile::class, [
            'getClientMimeType'     => 'image/jpeg',
            'getClientOriginalName' => 'foo.jpg'
        ]);
        $file->shouldReceive('move')->once()->with($fsPath, time() . '-foo.jpg');
        $thumbnail = m::mock(Thumbnail::class);
        $thumbnail->shouldReceive('isPhoto')->andReturn(1);
        $thumbnail->shouldReceive('make')->once();

        $form = new StoreFileablefiles($fileable_type, $fileable_id, $file, $thumbnail);
        $form->save();

    }

    public function non_image_for_item($fileable_type, $fileable_id)
    {

        $fsPath = new \App\Repos\Files\File();
        $fsPath = $fsPath->getSystemPath();
        $file = m::mock(UploadedFile::class, [
            'getClientMimeType'     => 'text/html',
            'getClientOriginalName' => 'foo.html'
        ]);
        $file->shouldReceive('move')->once()->with($fsPath, time() . '-foo.html');
        $thumbnail = m::mock(Thumbnail::class);
        $thumbnail->shouldReceive('isPhoto')->andReturn(0);
        $thumbnail->shouldNotReceive('make');

        $form = new StoreFileablefiles($fileable_type, $fileable_id, $file, $thumbnail);

        $form->save();

    }
}


function time()
{

    return 'now';
}
