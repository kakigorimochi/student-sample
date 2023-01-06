<?php

namespace App\Imports;

use App\Models\StudentGrade;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentGradesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new StudentGrade([
            "stud_no" => $row['stud_no'],
            "name" => $row['name'],
            "email" => $row['email'],
            "grade" => $row['grade'],
            "status" => $row['status']
        ]);
    }
}
