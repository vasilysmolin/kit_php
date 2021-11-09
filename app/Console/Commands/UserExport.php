<?php

namespace App\Console\Commands;

use App\Exports\ExportUsers;
use App\Models\User;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class UserExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $users = User::get();
        $collectionToExport = new ExportUsers($users->filter(function ($value) {
            return $value->email_verified_at !== null;
        })->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email_verified_at' => $user->email_verified_at,
                    'email' => $user->email,
                ];
        }));

        Excel::store($collectionToExport, "users.csv", 'local');
        return 0;
    }
}
