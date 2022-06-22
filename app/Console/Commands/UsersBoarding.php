<?php

namespace App\Console\Commands;

use App\Mail\NotifyStepsUserEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class UsersBoarding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users-boarding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'users-boarding';

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
        $usersIsNotPerson = User::whereDate('created_at', Carbon::now()->subDay())
            ->whereHas('profile', function ($q) {
                $q->where('isPerson', false);
            })
            ->whereNull('phone')
            ->get();
        $usersIsPerson = User::whereDate('created_at', Carbon::now()->subDay())
            ->whereHas('profile', function ($q) {
                $q->where('isPerson', true);
                $q->whereHas('person', function ($q) {
                    $q->whereNull('inn');
                });
            })
            ->get();

        $allUsers = $usersIsNotPerson->merge($usersIsPerson);

        $allUsers->map(function ($user) {
            Mail::to($user->email)->queue(new NotifyStepsUserEmail($user));
        });
    }
}
