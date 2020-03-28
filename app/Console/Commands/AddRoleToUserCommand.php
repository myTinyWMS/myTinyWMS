<?php

namespace Mss\Console\Commands;

use Illuminate\Console\Command;
use Mss\Models\User;
use Spatie\Permission\Models\Role;

class AddRoleToUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:assign-role-to-user {rolename} {userid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns the Role given by name to the user given by id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        User::findOrFail($this->argument('userid'))->assignRole($this->argument('rolename'));
    }
}
