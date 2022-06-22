<?php

namespace App\Console\Commands;

use App\Models\CatalogAd;
use App\Models\CatalogAdCategory;
use App\Models\CatalogMeta;
use App\Models\JobsResume;
use App\Models\JobsResumeCategory;
use App\Models\JobsVacancy;
use App\Models\Profile;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Objects\Education\Constants\Education;
use App\Objects\Files;
use App\Objects\SalaryType\Constants\SalaryType;
use App\Objects\Time\Constants\TimeArray;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sorting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sorting';

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
//        $users = User::get();
//        $users->map(function ($user) {
//            $user->email = Str::lower($user->email);
//            var_dump($user->email);
//            if (strlen($user->phone) < 10) {
//                $phone = null;
//            }
//            if (strlen($user->phone) === 10) {
//                $phone = $user->phone;
//            }
//            if (strlen($user->phone) === 11) {
//                $phone = substr($user->phone, 1);
//            }
//            if (strlen($user->phone) === 12) {
//                $phone = substr($user->phone, 2);
//            }
//            if (strlen($user->phone) > 12) {
//                $phone = null;
//            }
//            $user->phone = $phone;
//            $user->update();
//        });

        $items = User::orderBy('sort', 'ASC')->get();
        $i = 1;
        $items->map(function ($item) use (&$i) {
            $item->sort = $i;
            $item->update();
            $i++;
        });

        $items = JobsVacancy::orderBy('sort', 'ASC')->get();
        $i = 1;
        $items->map(function ($item) use (&$i) {
            $item->sort = $i;
            $item->update();
            $i++;
        });

        $items = JobsResume::orderBy('sort', 'ASC')->get();
        $i = 1;
        $items->map(function ($item) use (&$i) {
            $item->sort = $i;
            $item->update();
            $i++;
        });
        $items = Service::orderBy('sort', 'ASC')->get();
        $i = 1;
        $items->map(function ($item) use (&$i) {
            $item->sort = $i;
            $item->update();
            $i++;
        });

        $items = CatalogAd::orderBy('sort', 'ASC')->get();
        $i = 1;
        $items->map(function ($item) use (&$i) {
            $item->sort = $i;
            $item->update();
            $i++;
        });
//        dd(1);
//        $role = Role::where('name', 'admin')->first();
//        if (!isset($role)) {
//            Role::create(['name' => 'admin']);
//        }
//        User::where('email', 'tapigo@mail.ru')->first()->assignRole('admin');

        return 1;
    }
}
