<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Theme;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        // Получаем студентов и темы для текущей группы старосты
        // Пока показываем всех студентов и все темы
        $students = Student::with('theme')->orderBy('name')->get();
        $themes = Theme::orderBy('title')->get();
        
        return view('leader.assignments', compact('students', 'themes'));
    }

    public function create()
    {
        // Получаем свободные темы и студентов без тем
        $availableThemes = Theme::where('status', 'available')->orderBy('title')->get();
        $studentsWithoutThemes = Student::whereNull('theme_id')->orderBy('name')->get();
        
        return view('leader.assign', compact('availableThemes', 'studentsWithoutThemes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'theme_id' => 'required|exists:themes,id',
            'phone' => 'nullable|string|max:20',
        ]);

        $student = Student::findOrFail($request->student_id);
        $theme = Theme::findOrFail($request->theme_id);

        // Проверяем, что тема свободна
        if ($theme->status === 'assigned') {
            return redirect()->route('leader.assign')
                ->with('error', 'Эта тема уже назначена другому студенту.');
        }

        // Назначаем тему студенту
        $student->theme_id = $theme->id;
        if ($request->filled('phone')) {
            $student->phone = $request->phone;
        }
        $student->save();

        // Обновляем статус темы
        $theme->status = 'assigned';
        $theme->save();

        return redirect()->route('leader.assignments')
            ->with('success', 'Тема успешно назначена студенту!');
    }
}
