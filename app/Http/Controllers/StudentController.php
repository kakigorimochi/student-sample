<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentGradesImport;
use App\Models\StudentGrade;

class StudentController extends Controller
{
    public function index()
    {
        return view('student.index');
    }

    public function import_grades(Request $request)
    {
        Excel::import(new StudentGradesImport, $request->file('file'));
        return response()->json(['status' => 1, 'message' => 'task failed successfully']);
    }

    public function view_grades()
    {
        $query = StudentGrade::orderBy('name')->get();
        return response()->json($query);
    }
}
