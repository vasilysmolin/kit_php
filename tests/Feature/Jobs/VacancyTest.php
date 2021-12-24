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
            'vacancy',
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
        $restaraunt = JobsVacancy::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('vacancies.destroy', [$restaraunt->id]), []);

        $this->assertNull(JobsVacancy::find($restaraunt->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
