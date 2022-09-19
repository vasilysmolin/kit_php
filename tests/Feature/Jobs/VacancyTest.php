<?php

namespace Tests\Feature\Jobs;

use App\Models\JobsVacancy;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group vacancy
 * @group ci
 * */
class VacancyTest extends TestCase
{
    use DatabaseTransactions;

    public function testVacancyIndex()
    {
        $response = $this->get(route('vacancies.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'jobs_vacancies',
        ]);
    }

    public function testVacancyShow()
    {
        $vacancy = JobsVacancy::factory()->create();

        $response = $this->get(route('vacancies.show', $vacancy->id));
        $response->assertStatus(200);
    }

    public function testVacancyShow404()
    {
        $vacancy = JobsVacancy::factory()->create();

        $response = $this->get(route('vacancies.show', $vacancy->id . $vacancy->id));
        $response->assertStatus(404);
    }

    public function testVacancyUpdate()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $vacancy = JobsVacancy::factory(2)->create()->first();
        $vacancy->profile_id = $user->profile->getKey();
        $vacancy->sort = 1;
        $vacancy->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('vacancies.update', $vacancy->id), [
            'name' => 'newName',
        ]);
        $vacancy = JobsVacancy::find($vacancy->id);
        $this->assertEquals('newName', $vacancy->name);
        $this->assertEquals($vacancy->sort, 1);
        $response->assertStatus(204);
    }

    public function testVacancyState()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $vacancy = JobsVacancy::factory(2)->create()->first();
        $vacancy->sort = 1;
        $vacancy->profile_id = $user->profile->getKey();
        $vacancy->update();
        $sort = $vacancy->sort;
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('vacancies.state', $vacancy->id), [
            'state' => 'pause',
        ]);
        $vacancy = JobsVacancy::find($vacancy->id);
        $this->assertEquals('pause', $vacancy->state);
        $this->assertNotEquals($vacancy->sort, $sort);
        $response->assertStatus(204);
    }

    public function testStoreVacancy()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('vacancies.store'), [
                'name' => 'test',
                'max_price' => 1000,
                'min_price' => 100,
                'profile_id' => $user->profile->getKey(),
            ]);

        $id = explode('/vacancies/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('jobs_vacancies', [ 'id' => $id[1] ]);
        $vacancy = JobsVacancy::find($id[1]);
        $this->assertEquals(1, $vacancy->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyVacancy()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $vacancy = JobsVacancy::factory()->create();
        $vacancy->profile_id = $user->profile->getKey();
        $vacancy->update();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('vacancies.destroy', [$vacancy->id]), []);

        $this->assertNull(JobsVacancy::find($vacancy->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testRestoreVacancy()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $vacancy = JobsVacancy::factory(4)->create()->first();
        $vacancy->profile_id = $user->profile->getKey();
        $vacancy->update();
        $access_token = JWTAuth::fromUser($user);
        $vacancy->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('vacancies.restore', [$vacancy->id]), []);

        $this->assertDatabaseHas('jobs_vacancies', [ 'id' => $vacancy->id ]);
        $vacancySort = JobsVacancy::orderBy('sort', 'ASC')->first();
        $this->assertEquals($vacancySort->getKey(), $vacancy->id);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testSortVacancy()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $vacancy = JobsVacancy::factory()->create();
        $vacancy->profile_id = $user->profile->getKey();
        $vacancy->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('vacancies.sort', [$vacancy->id]), []);
        $vacancy = JobsVacancy::find($vacancy->id);
        $this->assertEquals(1, $vacancy->sort);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testStoreVacancyAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $userTwo = User::factory()->has(Profile::factory())->create();
        $response = $this
            ->withToken($access_token)
            ->json('POST', route('vacancies.store'), [
                'name' => 'test',
                'max_price' => 1000,
                'min_price' => 100,
                'profile_id' => $userTwo->profile->getKey(),
            ]);

        $id = explode('/vacancies/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('jobs_vacancies', [ 'id' => $id[1] ]);
        $vacancy = JobsVacancy::find($id[1]);
        $this->assertEquals(1, $vacancy->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyVacancyAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $vacancy = JobsVacancy::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('vacancies.destroy', [$vacancy->id]), []);

        $this->assertNull(JobsVacancy::find($vacancy->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testVacancyUpdateAdmin()
    {
        $vacancy = JobsVacancy::factory(2)->create()->first();
        $vacancy->sort = 1;
        $vacancy->update();
        $sort = $vacancy->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('vacancies.update', $vacancy->id), [
            'name' => 'newName',
        ]);
        $vacancy = JobsVacancy::find($vacancy->id);
        $this->assertEquals('newName', $vacancy->name);
        $this->assertEquals($vacancy->sort, $sort);
        $response->assertStatus(204);
    }

    public function testVacancyStateAdmin()
    {
        $vacancy = JobsVacancy::factory(2)->create()->first();
        $vacancy->sort = 1;
        $vacancy->update();
        $sort = $vacancy->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('vacancies.state', $vacancy->id), [
            'state' => 'new',
        ]);
        $vacancy = JobsVacancy::find($vacancy->id);
        $this->assertEquals('new', $vacancy->state);
        $this->assertNotEquals($vacancy->sort, $sort);
        $response->assertStatus(204);
    }

    public function testVacancyUpdate403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $vacancy = JobsVacancy::factory(2)->create()->first();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('vacancies.update', $vacancy->id), [
            'name' => 'newName',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDestroyVacancy403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $vacancy = JobsVacancy::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('vacancies.destroy', [$vacancy->id]), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testRestoreVacancy403()
    {
        $vacancy = JobsVacancy::factory(4)->create()->first();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $vacancy->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('vacancies.restore', [$vacancy->id]), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testSortVacancy403()
    {
        $vacancy = JobsVacancy::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('vacancies.sort', [$vacancy->id]), []);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testVacancyState403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $vacancy = JobsVacancy::factory(2)->create()->first();

        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('vacancies.state', $vacancy->id), [
            'state' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testVacancyState403byState()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $vacancy = JobsVacancy::factory(2)->create()->first();
        $vacancy->profile_id = $user->profile->getKey();
        $vacancy->update();

        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('vacancies.state', $vacancy->id), [
            'state' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testVacancyIndexCabinet()
    {
        $count = 1;
        $user = User::factory()->has(Profile::factory())->create();
        JobsVacancy::factory(4)->create()->first();
        $vacancy = JobsVacancy::factory($count)->create()->first();
        $vacancy->profile_id = $user->profile->getKey();
        $vacancy->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->get(route('vacancies.index',['from'=> 'cabinet']));
        $response->assertStatus(200)->assertJsonStructure([
            'jobs_vacancies',
        ]);
        $this->assertEquals($count, count(json_decode($response->getContent(), true)['jobs_vacancies']));
    }

    public function testVacancyShowCabinet()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $vacancy = JobsVacancy::factory()->create();
        $vacancy->profile_id = $user->profile->getKey();
        $vacancy->update();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->get(route('vacancies.show', [$vacancy->id, 'from' => 'cabinet']));
        $response->assertStatus(200);
    }

    public function testVacancyShowCabinet404()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $vacancy = JobsVacancy::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->get(route('vacancies.show', [$vacancy->id, 'from' => 'cabinet']));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
