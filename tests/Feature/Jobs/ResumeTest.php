<?php

namespace Tests\Feature\Jobs;

use App\Models\JobsResume;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
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

    public function testResumeShow404()
    {
        $resume = JobsResume::factory()->create();

        $response = $this->get(route('resume.show', $resume->id . $resume->id));
        $response->assertStatus(404);
    }

    public function testStoreResume()
    {
//        $category = JobsResumeCategory::factory()->create();
        $user = User::factory()->has(Profile::factory())->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('POST', route('resume.store'), [
                'name' => 'test',
//                'categoryID' => [$category->id],
            ]);

        $id = explode('/resumes/', $response->baseResponse->headers->get('Location'));
        $this->assertDatabaseHas('jobs_resumes', [ 'id' => $id[1] ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testDestroyResume()
    {
        $resume = JobsResume::factory()->create();
        $user = User::factory()->create();
        $access_token = JWTAuth::fromUser($user);

        $response = $this
            ->withToken($access_token)
            ->json('DELETE', route('resume.destroy', [$resume->id]), []);

        $this->assertNull(JobsResume::find($resume->id));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
