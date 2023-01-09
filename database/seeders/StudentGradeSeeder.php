<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Faker\Factory;

use App\Models\StudentGrade;

class StudentGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        StudentGrade::truncate();
        Schema::enableForeignKeyConstraints();

        StudentGrade::create([
            'stud_no' => 'ST-000',
            'name'    => 'Gian Javelona',
            'email'   => 'ceo@orangeappps.ph',
            'grade'   => '100',
            'conduct' => '4',
        ]);

        $faker = Factory::create();
        for ($i = 11; $i < 49; $i += 1)
        {
            StudentGrade::create([
                'stud_no' => 'ST-0' . $i,
                'name'    => $faker->name(),
                'email'   => $faker->email(),
                'grade'   => rand(70, 90),
                'conduct' => rand(1, 5),
            ]);
        };
    }
}
