<?php

namespace Tests\Feature\Common\Select;

use App\Objects\Education\Constants\Education;
use App\Objects\SalaryType\Constants\SalaryType;
use App\Objects\Schedule\Constants\Schedule;
use App\Objects\Time\Constants\TimeArray;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * @group select
 * @group ci
 * */
class SelectTest extends TestCase
{
    use DatabaseTransactions;

    public function testExperience()
    {
        $response = $this->get(route('select.experience'));
        $resp = json_decode($response->getContent(), true);
        $data = (new TimeArray())->get();

        $response->assertStatus(200);
        $this->assertEquals($data, $resp);
    }

    public function testEducations()
    {
        $response = $this->get(route('select.educations'));
        $resp = json_decode($response->getContent(), true);
        $data = (new Education())->get();

        $response->assertStatus(200);
        $this->assertEquals($data, $resp);
    }

    public function testSchedules()
    {
        $response = $this->get(route('select.schedules'));
        $resp = json_decode($response->getContent(), true);
        $data = (new Schedule())->get();

        $response->assertStatus(200);
        $this->assertEquals($data, $resp);
    }

    public function testSalary()
    {
        $response = $this->get(route('select.salary'));
        $resp = json_decode($response->getContent(), true);
        $data = (new SalaryType())->get();

        $response->assertStatus(200);
        $this->assertEquals($data, $resp);
    }
}
