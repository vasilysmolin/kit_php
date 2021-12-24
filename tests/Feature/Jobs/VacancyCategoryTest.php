<?php

namespace Tests\Feature\Jobs;

use App\Models\JobsVacancyCategory;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group category-vacancies
 * @group ci
 * */
class VacancyCategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testVacancyCategoryIndex()
    {
        $response = $this->get(route('category-vacancies.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'category_vacancies',
        ]);
    }

//    public function testVacancyCategoryShow()
//    {
//        $categoryVacancies = JobsVacancyCategory::factory()->create();
//
//        $response = $this->get(route('category-vacancies.show', [$categoryVacancies->id]));
//        dd($response->getContent());
//        $response->assertStatus(200);
//    }

    public function testVacancyCategoryShow404()
    {
        $categoryVacancies = JobsVacancyCategory::factory()->create();

        $response = $this->get(route('category-vacancies.show', $categoryVacancies->id . $categoryVacancies->id));
        $response->assertStatus(404);
    }

    public function testStoreVacancyCategory()
    {

        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('category-vacancies.store'), [
                'name' => 'test',
            ]);

        $id = explode('/category-vacancies/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('jobs_vacancy_categories', [ 'id' => $id[1] ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyVacancyCategory()
    {
        $categoryResume = JobsVacancyCategory::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('category-vacancies.destroy', [$categoryResume->id]), []);

        $this->assertNull(JobsVacancyCategory::find($categoryResume->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
