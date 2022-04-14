<?php

namespace Tests\Feature\Jobs;

use App\Models\JobsVacancy;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
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

    public function testStoreVacancy()
    {
//        $category = JobsVacancyCategory::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('vacancies.store'), [
                'name' => 'test',
                'max_price' => 1000,
                'min_price' => 100,
//                'categoryID' => [$category->id],
            ]);

        $id = explode('/vacancies/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('jobs_vacancies', [ 'id' => $id[1] ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * @group restaurant1
     * @group ci
     * */
    public function testDestroyVacancy()
    {
        $vacancy = JobsVacancy::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('vacancies.destroy', [$vacancy->id]), []);

        $this->assertNull(JobsVacancy::find($vacancy->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testRestoreVacancy()
    {
        $vacancy = JobsVacancy::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);
        $vacancy->delete();
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('vacancies.restore', [$vacancy->id]), []);

        $this->assertDatabaseHas('jobs_vacancies', [ 'id' => $vacancy->id ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testSortVacancy()
    {
        $vacancy = JobsVacancy::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);
        $response = $this
            ->withToken($access_token)
            ->json('PUT', route('vacancies.sort', [$vacancy->id]), []);
        $vacancy = JobsVacancy::find($vacancy->id);
        $this->assertEquals(1, $vacancy->sort);
        $response->assertStatus(Response::HTTP_OK);
    }
}
