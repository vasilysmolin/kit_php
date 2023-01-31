<?php

namespace Tests\Feature\Journal;

use App\Models\Journal;
use App\Models\JournalCategory;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group journal
 * @group ci
 * */
class JournalTest extends TestCase
{
    use DatabaseTransactions;

    public function testJournalIndex()
    {
        $response = $this->get(route('journals.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'journals',
        ]);
    }

    public function testJournalShow()
    {
        $journal = Journal::factory()->create();

        $response = $this->get(route('journals.show', [$journal->id]));

        $response->assertStatus(200);
    }

    public function testJournalShow404()
    {
        $response = $this->get(route('journals.show', "00"));
        $response->assertStatus(404);
    }

    public function testJournalUpdate()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $journal = Journal::factory(2)->create()->first();
        $journal->sort = 1;
        $journal->profile_id = $user->profile->getKey();
        $journal->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('journals.update', $journal->getKey()), [
            'name' => 'newName',
        ]);
        $journal = Journal::find($journal->id);
        $this->assertEquals('newName', $journal->name);
        $this->assertEquals($journal->sort, 1);
        $response->assertStatus(204);
    }

    public function testJournalState()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $journal = Journal::factory(2)->create()->first();
        $journal->sort = 1;
        $journal->profile_id = $user->profile->getKey();
        $journal->update();
        $sort = $journal->sort;
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('journals.state', $journal->id), [
            'state' => 'pause',
        ]);
        $journal = Journal::find($journal->id);
        $this->assertEquals('pause', $journal->state);
        $this->assertNotEquals($journal->sort, $sort);
        $response->assertStatus(204);
    }

    public function testStoreJournal()
    {

        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $cat = JournalCategory::factory()->create()->first();

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('journals.store'), [
                'name' => 'test',
                'price' => 3000,
                'category_id' => $cat->getKey(),
            ]);
        $id = explode('/journals/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('journals', [ 'id' => $id[1] ]);
        $journal = Journal::find($id[1]);
        $this->assertEquals(1, $journal->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyJournal()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $journal = Journal::factory()->create();
        $journal->profile_id = $user->profile->getKey();

        $journal->update();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('journals.destroy', [$journal->id]), []);

        $this->assertNull(Journal::find($journal->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testRestoreJournal()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $journal = Journal::factory(4)->create()->first();
        $journal->profile_id = $user->profile->getKey();

        $journal->update();
        $access_token = JWTAuth::fromUser($user);
        $journal->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('journals.restore', [$journal->id]), []);

        $this->assertDatabaseHas('journals', [ 'id' => $journal->id ]);
        $journalSort = Journal::orderBy('sort', 'ASC')->first();
        $this->assertEquals($journalSort->getKey(), $journal->id);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testSortJournal()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $journal = Journal::factory()->create();
        $journal->profile_id = $user->profile->getKey();

        $journal->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('journals.sort', [$journal->id]), []);
        $journal = Journal::find($journal->id);
        $this->assertEquals(1, $journal->sort);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testStoreJournalAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $cat = JournalCategory::factory()->create()->first();;
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $userTwo = User::factory()->has(Profile::factory())->create();
        $response = $this
            ->withToken($access_token)
            ->json('POST', route('journals.store'), [
                'name' => 'test',
                'price' => 3000,
                'category_id' => $cat->getKey(),
                'profile_id' => $userTwo->profile->getKey(),
            ]);
        $id = explode('/journals/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('journals', [ 'id' => $id[1] ]);
        $journal = Journal::find($id[1]);
        $this->assertEquals(1, $journal->sort);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyJournalAdmin()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $journal = Journal::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('journals.destroy', [$journal->id]), []);

        $this->assertNull(Journal::find($journal->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testJournalUpdateAdmin()
    {
        $journal = Journal::factory(2)->create()->first();
        $journal->sort = 1;
        $journal->update();
        $sort = $journal->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('journals.update', $journal->id), [
            'name' => 'newName',
        ]);
        $journal = Journal::find($journal->id);
        $this->assertEquals('newName', $journal->name);
        $this->assertEquals($journal->sort, $sort);
        $response->assertStatus(204);
    }

    public function testJournalStateAdmin()
    {
        $journal = Journal::factory(2)->create()->first();
        $journal->sort = 1;
        $journal->update();
        $sort = $journal->sort;
        $user = User::factory()->has(Profile::factory())->create();
        $role = Role::where('name', 'admin')->first();
        if (!isset($role)) {
            Role::create(['name' => 'admin']);
        }
        $user->assignRole('admin');
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('journals.state', $journal->id), [
            'state' => 'new',
        ]);
        $journal = Journal::find($journal->id);
        $this->assertEquals('new', $journal->state);
        $this->assertNotEquals($journal->sort, $sort);
        $response->assertStatus(204);
    }

    public function testJournalUpdate403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $journal = Journal::factory(2)->create()->first();
        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('journals.update', $journal->id), [
            'name' => 'newName',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDestroyJournal403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $journal = Journal::factory()->create()->first();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('journals.destroy', [$journal->id]), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testRestoreJournal403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $journal = Journal::factory(2)->create()->first();
        $access_token = JWTAuth::fromUser($user);
        $journal->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('journals.restore', [$journal->id]), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testSortJournal403()
    {
        $journal = Journal::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('journals.sort', [$journal->id]), []);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testJournalState403()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $journal = Journal::factory(2)->create()->first();

        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('journals.state', $journal->id), [
            'state' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testJournalState403byState()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $journal = Journal::factory(2)->create()->first();
        $journal->profile_id = $user->profile->getKey();
        $journal->update();

        $access_token = JWTAuth::fromUser($user);
        $response = $this->withToken($access_token)->put(route('journals.state', $journal->id), [
            'state' => 'new',
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testJournalIndexCabinet()
    {
        $count = 1;
        $user = User::factory()->has(Profile::factory())->create();
        Journal::factory(4)->create()->first();
        $journal = Journal::factory($count)->create()->first();
        $journal->profile_id = $user->profile->getKey();
        $journal->update();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->get(route('journals.index', ['from' => 'cabinet']));
        $response->assertStatus(200)->assertJsonStructure([
            'journals',
        ]);
        $this->assertEquals($count, count(json_decode($response->getContent(), true)['journals']));
    }

    public function testJournalShowCabinet()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $journal = Journal::factory()->create();
        $journal->profile_id = $user->profile->getKey();
        $journal->update();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->get(route('journals.show', [$journal->id, 'from' => 'cabinet']));
        $response->assertStatus(200);
    }

    public function testJournalShowCabinet404()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $journal = Journal::factory(3)->create()->first();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->get(route('journals.show', [$journal->id, 'from' => 'cabinet']));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
