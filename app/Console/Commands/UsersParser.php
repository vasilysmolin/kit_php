<?php

namespace App\Console\Commands;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UsersParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'user-parse';

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

        $client = new Client();
        $response = $client->get('https://user.tapigo.ru/all-users-json', ['verify' => false]);
        $contents = $response->getBody()->getContents();
        $contents = json_decode($contents, true);
//        DB::transaction(function () use ($contents) {
        foreach ($contents as $item) {
            $userDB = User::find($item['id']);
            if(isset($userDB)) {
                $user = $userDB;
                $user->id = $item['id'];
                $user->email = $item['email'];
                $user->password = $item['password'];
                $user->phone = $item['phone'];
                $user->name = $item['profile']['name'];
                $user->update();
            } else {
                $count = User::where('email', $item['email'])->get();

                if ($count->count() === 0) {
                    $user = new User();
                    $user->id = $item['id'];
                    $user->email = $item['email'];
                    $user->password = $item['password'];
                    $user->phone = $item['phone'];
                    $user->name = $item['profile']['name'];
                    $user->save();
                }
            }

        }
//        },2);

        return 1;
    }
}
