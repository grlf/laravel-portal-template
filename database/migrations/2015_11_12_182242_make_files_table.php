<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fileable_type');
            $table->integer('fileable_id');
            $table->text('filepath');
            $table->string('filename');
            $table->string('filetype');
            $table->string('filesize');
            $table->string('thumbnail');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('files');
    }
}
