<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $skills = [
            [
                'id'         => 1,
                'name'       => 'PHP'
            ],
            [
                'id'         => 2,
                'name'       => 'Java'
            ],
            [
                'id'         => 3,
                'name'       => 'Python'
            ],
            [
                'id'         => 4,
                'name'       => 'C'
            ],
            [
                'id'         => 5,
                'name'       => 'C++'
            ]
        ];

        Skill::insert($skills);
    }
}
