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
        // Получаем группу старосты через его email
        $leaderEmail = auth()->user()->email;
        $leaderStudent = Student::where('email', $leaderEmail)->first();
        
        if ($leaderStudent) {
            // Фильтруем студентов по группе старосты
            $students = Student::where('group', $leaderStudent->group)
                ->with('theme')
                ->orderBy('name')
                ->get();
        } else {
            // Если староста не найден в таблице студентов, показываем всех
            $students = Student::with('theme')->orderBy('name')->get();
        }
        
        $themes = Theme::orderBy('title')->get();
        
        return view('leader.assignments', compact('students', 'themes'));
    }

    public function create()
    {
        // Получаем группу старосты через его email
        $leaderEmail = auth()->user()->email;
        $leaderStudent = Student::where('email', $leaderEmail)->first();
        
        // Получаем свободные темы
        $availableThemes = Theme::where('status', 'available')->orderBy('title')->get();
        
        // Получаем студентов без тем
        $query = Student::whereNull('theme_id');
        
        // Фильтруем по группе старосты, если он найден в таблице студентов
        if ($leaderStudent) {
            $query->where('group', $leaderStudent->group);
        }
        
        $studentsWithoutThemes = $query->orderBy('name')->get();
        
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
        
        // Проверяем, что староста может назначать темы только студентам своей группы
        $leaderEmail = auth()->user()->email;
        $leaderStudent = Student::where('email', $leaderEmail)->first();
        
        if ($leaderStudent && $student->group !== $leaderStudent->group) {
            return redirect()->route('leader.assign')
                ->with('error', 'Вы можете назначать темы только студентам своей группы.');
        }

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
