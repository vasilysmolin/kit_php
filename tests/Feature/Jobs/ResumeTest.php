<?php

namespace Tests\Feature\Jobs;

use App\Models\JobsResume;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group resume
 * @group ci
 * */
class ResumeTest extends TestCase
{
    use DatabaseTransactions;


    public function testResumeIndex()
    {
        $response = $this->get(route('resume.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'jobs_resumes',
        ]);
    }

    public function testResumeShow()
    {
        $resume = JobsResume::factory()->create();

        $response = $this->get(route('resume.show', $resume->id));
        $response->assertStatus(200);
    }

    public function testResumeUpdate()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $resume = JobsResume::factory(2)->create()->first();
        $resume->profile_id = $user->profile->getKey();
        $resume->sort = 1;
        $resume->update();
        $sort = $resume->sort;
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('resume.update', $resume->id), [
            'name' => 'newName',
        ]);
        $resume = JobsResume::find($resume->id);
        $this->assertEquals('newName', $resume->name);
        $this->assertEquals($resume->sort, $sort);
        $response->assertStatus(204);
    }

    public function testResumeState()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $resume = JobsResume::factory(2)->create()->first();
        $resume->profile_id = $user->profile->getKey();
        $resume->sort = 1;
        $resume->update();
        $sort = $resume->sort;
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('resume.state', $resume->getKey()), [
            'state' => 'pause',
        ]);
        $resume = JobsResume::find($resume->id);
        $this->assertEquals('pause', $resume->state);
        $this->assertNotEquals($resume->sort, $sort);
        $response->assertStatus(204);
    }

    public function testResumeShow404()
    {
        $resume = JobsResume::factory()->create();

        $response = $this->get(route('resume.show', $resume->id . $resume->id));
        $response->assertStatus(404);
    }

    public function testStoreResume()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('resume.store'), [
                'name' => 'test',
            ]);
        $id = explode('/resumes/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('jobs_resumes', [ 'id' => $id[1] ]);
        $resume = JobsResume::find($id[1]);
        $this->assertEquals(1, $resume->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyResume()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $resume = JobsResume::factory()->create();
        $resume->profile_id = $user->profile->getKey();
        $resume->sort = 1;
        $resume->update();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('resume.destroy', [$resume->id]), []);

        $this->assertNull(JobsResume::find($resume->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testRestoreResume()
    {
        $resume = JobsResume::factory(5)->create()->first();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $resume->profile_id = $user->profile->getKey();
        $resume->update();
        $resume->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('resume.restore', [$resume->id]), []);

        $this->assertDatabaseHas('jobs_resumes', [ 'id' => $resume->id ]);
        $resumeSort = JobsResume::orderBy('sort', 'ASC')->first();
        $this->assertEquals($resumeSort->getKey(), $resume->id);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testSortResume()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $resume = JobsResume::factory()->create();
        $resume->profile_id = $user->profile->getKey();
        $resume->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('resume.sort', [$resume->id]), []);
        $resume = JobsResume::find($resume->id);
        $this->assertEquals(1, $resume->sort);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testStoreResumeAdmin()
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
            ->json('POST', route('resume.store'), [
                'name' => 'test',
                'max_price' => 1000,
                'min_price' => 100,
                'profile_id' => $userTwo->profile->getKey(),
            ]);

        $id = explode('/resumes/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('jobs_resumes', [ 'id' => $id[1] ]);
        $resume = JobsResume::find($id[1]);
        $this->assertEquals(1, $resume->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyResumeAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $resume = JobsResume::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('resume.destroy', [$resume->id]), []);

        $this->assertNull(JobsResume::find($resume->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testResumeUpdateAdmin()
    {
        $resume = JobsResume::factory(2)->create()->first();
        $resume->sort = 1;
        $resume->update();
        $sort = $resume->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('resume.update', $resume->id), [
            'name' => 'newName',
        ]);
        $resume = JobsResume::find($resume->id);
        $this->assertEquals('newName', $resume->name);
        $this->assertEquals($resume->sort, $sort);
        $response->assertStatus(204);
    }

    public function testResumeStateAdmin()
    {
        $resume = JobsResume::factory(2)->create()->first();
        $resume->sort = 1;
        $resume->update();
        $sort = $resume->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('resume.state', $resume->id), [
            'state' => 'new',
        ]);
        $resume = JobsResume::find($resume->id);
        $this->assertEquals('new', $resume->state);
        $this->assertNotEquals($resume->sort, $sort);
        $response->assertStatus(204);
    }

    public function testResumeUpdate403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $resume = JobsResume::factory(2)->create()->first();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('resume.update', $resume->id), [
            'name' => 'newName',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDestroyResume403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $resume = JobsResume::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('resume.destroy', [$resume->id]), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testRestoreResume403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $resume = JobsResume::factory(2)->create()->first();
        $access_token = JWTAuth::fromUser($user);
        $resume->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('resume.restore', [$resume->id]), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testSortResume403()
    {
        $resume = JobsResume::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('resume.sort', [$resume->id]), []);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testResumeState403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $resume = JobsResume::factory(2)->create()->first();

        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('resume.state', $resume->id), [
            'state' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testResumeState403byState()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $resume = JobsResume::factory(2)->create()->first();
        $resume->profile_id = $user->profile->getKey();
        $resume->update();

        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('resume.state', $resume->id), [
            'state' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testStoreResume403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $userTwo = User::factory()->has(Profile::factory())->create();
        $response = $this
            ->withToken($access_token)
            ->json('POST', route('resume.store'), [
                'name' => 'test',
                'max_price' => 1000,
                'min_price' => 100,
                'profile_id' => $userTwo->profile->getKey(),
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testResumeIndexCabinet()
    {
        $count = 1;
        $user = User::factory()->has(Profile::factory())->create();
        JobsResume::factory(4)->create()->first();
        $resume = JobsResume::factory($count)->create()->first();
        $resume->profile_id = $user->profile->getKey();
        $resume->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->get(route('resume.index',['from'=> 'cabinet']));
        $response->assertStatus(200)->assertJsonStructure([
            'jobs_resumes',
        ]);
        $this->assertEquals($count, count(json_decode($response->getContent(), true)['jobs_resumes']));
    }

    public function testResumeShowCabinet()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $resume = JobsResume::factory()->create();
        $resume->profile_id = $user->profile->getKey();
        $resume->update();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->get(route('resume.show', [$resume->id, 'from' => 'cabinet']));
        $response->assertStatus(200);
    }

    public function testResumeShowCabinet404()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $resume = JobsResume::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->get(route('resume.show', [$resume->id, 'from' => 'cabinet']));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
