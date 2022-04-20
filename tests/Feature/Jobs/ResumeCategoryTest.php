<?php

namespace Tests\Feature\Jobs;

use App\Models\JobsResumeCategory;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group category-resume
 * @group ci
 * */
class ResumeCategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testRestaurantIndex()
    {
        $response = $this->get(route('category-resume.index'));
        $response->assertStatus(200)->assertJsonStructure([
            'jobs_resumes_categories',
        ]);
    }

    public function testRestaurantShow()
    {
        $resume = JobsResumeCategory::factory()->create();

        $response = $this->get(route('category-resume.show', $resume->id));
        $response->assertStatus(200);
    }

    public function testRestaurantShow404()
    {
        $resume = JobsResumeCategory::factory()->create();

        $response = $this->get(route('category-resume.show', $resume->id . $resume->id));
        $response->assertStatus(404);
    }

    public function testStoreResume()
    {

        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('category-resume.store'), [
                'name' => 'test',
            ]);

        $id = explode('/category-resume/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('jobs_resumes_categories', [ 'id' => $id[1] ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyResume()
    {
        $categoryResume = JobsResumeCategory::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('category-resume.destroy', [$categoryResume->id]), []);

        $this->assertNull(JobsResumeCategory::find($categoryResume->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
