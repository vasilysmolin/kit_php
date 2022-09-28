<?php

namespace Tests\Feature\User;


use App\Models\Profile;
use App\Models\User;
use App\Objects\States\States;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group users
 * @group ci
 * */
class UserTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    private User $user;
    private string $token;
    private array $userIndex = [
        'meta' => [
            'skip',
            'limit',
            'total',
        ],
        'users' => [
            [
                'id',
                'name',
                'email',
                'phone',
                'city_id',
                'profile' => [
                    'person',
                ],
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $roleModel = Role::where('name', 'admin')->first();
        $role = empty($roleModel) ? Role::create(['name' => 'admin']) : $roleModel;
        $user =  User::factory(1)
            ->has(Profile::factory(1))
            ->create()->first();
        $token = JWTAuth::fromUser($user);
        $user->assignRole($role);
        $this->user = $user;
        $this->token = $token;
    }

    public function testUsersIndexNotAuth()
    {
        $response = $this->get(route('users.index'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUsersIndex()
    {
        $response = $this->withToken($this->token)->get(route('users.index'));
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure($this->userIndex);
    }

    public function testUsersShow()
    {
        $response = $this->withToken($this->token)->get(route('users.show', [$this->user]));
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure([
                'id',
                'name',
                'email',
                'phone',
                'city_id',
                'role',
                'profile' => [
                    'person',
                ],
            ]);
    }

    public function testUsersShowNotAuth()
    {
        $response = $this->get(route('users.show', [$this->user]));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUsersUpdateNotAuth()
    {
        $response = $this->put(route('users.update', [$this->user]));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUsersUpdate()
    {
        $stateNew = (new States())->new();
        $response = $this->withToken($this->token)->put(route('users.update', [$this->user]), [
            'name' => $this->faker->name,
            'state' => $stateNew,
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUsersUpdate422()
    {
        $response = $this->withToken($this->token)->put(route('users.update', [$this->user]), [
            'name' => $this->faker->name,
            'state' => $this->faker->name,
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testChangeProfile()
    {
        $user =  User::factory(1)
            ->has(Profile::factory(1))
            ->create()->first();
        $this->user->user_id = $this->user->getKey();
        $invitingUser = $user->profile->invitedAccounts()->make($this->user->toArray());
        $invitingUser->save();
        $claimsFromToken = [
            'profile_id' => $user->profile->getKey(),
            'id' => $user->getKey(),
        ];
        $response = $this->withToken($this->token)->put(route('users.change-profile'), $claimsFromToken);
        $newToken = $response->json()['access_token'];
        $claims = JWTAuth::setToken($newToken)->parseToken()->getCustomClaims();
        $this->assertTrue($claimsFromToken === $claims);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testAccounts()
    {
        $user = User::factory(1)
            ->has(Profile::factory(1))
            ->create()->first();
        $this->user->user_id = $this->user->getKey();
        $invitingUser = $user->profile->invitedAccounts()->make($this->user->toArray());
        $invitingUser->save();

        $response = $this->withToken($this->token)->get(route('users.accounts'));
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'meta' => [
                    'skip',
                    'limit',
                    'total',
                ],
                'users' => [
                    [
                        'id',
                        'user_id',
                        'email',
                        'profile' => [
                            'person',
                        ],
                    ],
                ],
            ]);
    }

    public function testCurrentAccount()
    {
        $user =  User::factory(1)
            ->has(Profile::factory(1))
            ->create()->first();
        $this->user->user_id = $this->user->getKey();
        $invitingUser = $user->profile->invitedAccounts()->make($this->user->toArray());
        $invitingUser->save();
        $claimsFromToken = [
            'profile_id' => $user->profile->getKey(),
            'id' => $user->getKey(),
        ];
        $response = $this->withToken($this->token)->put(route('users.change-profile'), $claimsFromToken);
        $newToken = $response->json()['access_token'];
        $this->token = $newToken;
        $response = $this->withToken($this->token)->get(route('users.current.account'));
        $this->assertTrue($user->getKey() === $response->json()['id']);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCheckUser()
    {
        $user =  User::factory(1)
            ->has(Profile::factory(1))
            ->create()->first();

        $response = $this->withToken($this->token)->get(route('users.check-user', [$user->email]));
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure(['success']);
    }

    public function testAddUser()
    {
        $user =  User::factory(1)
            ->has(Profile::factory(1))
            ->create()->first();

        $response = $this->withToken($this->token)->put(route('users.add-user', [$user->email]));
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure(['success']);
    }

    public function testDeleteUser()
    {
        $user =  User::factory(1)
            ->has(Profile::factory(1))
            ->create()->first();

        $response = $this->withToken($this->token)->delete(route('users.delete-user', [$user->email]));
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure(['success']);
    }


    public function testDownloadNotAuth()
    {
        $response = $this->get(route('users.download'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDownload()
    {
        $response = $this->withToken($this->token)->get(route('users.download'));
        $response->assertStatus(200);
    }
}
