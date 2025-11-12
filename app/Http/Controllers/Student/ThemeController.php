<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        // Находим студента по email текущего пользователя
        $student = Student::where('email', auth()->user()->email)
            ->with('theme.supervisor')  // Загружаем руководителя через тему
            ->first();
        
        return view('student.theme', compact('student'));
    }
}
