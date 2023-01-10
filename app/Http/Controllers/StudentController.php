<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentGradesImport;
use Illuminate\Support\Facades\Schema;
use Faker\Factory;
use App\Models\StudentGrade;

class StudentController extends Controller
{
    public function index()
    {
        return view('student.index');
    }

    public function import_grades(Request $request)
    {
        if ($request->file('file') == null)
            return response()->json(['status' => 2, 'message' => 'File cannot be empty']);

        Excel::import(new StudentGradesImport, $request->file('file'));
        return response()->json(['status' => 1, 'message' => 'File upload success']);
    }

    public function view_grades()
    {
        $query = StudentGrade::orderBy('grade', 'DESC')->get();
        return response()->json($query);
    }

    public function condition_grades_db(Request $request)
    {
        if ($request->agreed == true)
        {
            if ($request->truncate == true)
            {
                Schema::disableForeignKeyConstraints();
                StudentGrade::truncate();
                Schema::enableForeignKeyConstraints();
            }

            if ($request->seed == true)
            {
                StudentGrade::create([
                    'stud_no' => 'ST-000',
                    'name'    => 'Gian Javelona',
                    'email'   => 'ceo@orangeappps.ph',
                    'grade'   => '100',
                    'conduct' => '100',
                ]);

                $faker = Factory::create();
                for ($i = 11; $i < 49; $i += 1)
                {
                    StudentGrade::create([
                        'stud_no' => 'ST-0' . $i,
                        'name'    => $faker->name(),
                        'email'   => $faker->email(),
                        'grade'   => rand(70, 97),
                        'conduct' => rand(70, 97),
                    ]);
                };
            }

            return response()->json(['status' => 1, 'message' => 'Data conditioning success']);
        } else return response()->json(['status' => 2, 'message' => 'Please agree to the terms']);
    }
}
