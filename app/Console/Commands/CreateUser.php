<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal:create-user
                            {--p|password=testpass123 : The password for the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a user based on the Model Factory';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = factory(\App\User::class)->create([
            'password' => bcrypt($this->option('password')),
        ]);

        $this->line('Created new User with id: ' . $user->id);
    }
}
