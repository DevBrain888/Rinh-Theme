<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Models\Student;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
    public function distribute(Request $request)
    {
        // Получаем все доступные темы
        $availableThemes = Theme::where('status', 'available')
            ->whereNull('assigned_to')
            ->get();

        // Получаем всех студентов без назначенных тем
        $studentsWithoutThemes = Student::whereNull('theme_id')->get();

        if ($availableThemes->isEmpty()) {
            return redirect()->route('dashboard')
                ->with('error', 'Нет доступных тем для распределения. Загрузите темы в разделе "Управление темами".');
        }

        if ($studentsWithoutThemes->isEmpty()) {
            return redirect()->route('dashboard')
                ->with('error', 'Нет студентов без назначенных тем. Загрузите студентов в разделе "Управление студентами".');
        }

        // Перемешиваем темы и студентов случайным образом
        $themes = $availableThemes->shuffle();
        $students = $studentsWithoutThemes->shuffle();

        // Распределяем темы
        $count = 0;
        $minCount = min($themes->count(), $students->count());

        for ($i = 0; $i < $minCount; $i++) {
            $theme = $themes[$i];
            $student = $students[$i];

            // Назначаем тему студенту
            $student->theme_id = $theme->id;
            $student->save();

            // Обновляем статус темы
            $theme->status = 'assigned';
            $theme->save();

            $count++;
        }

        $message = "Успешно распределено тем: {$count}";
        if ($themes->count() > $students->count()) {
            $remaining = $themes->count() - $students->count();
            $message .= ". Осталось свободных тем: {$remaining}";
        } elseif ($students->count() > $themes->count()) {
            $remaining = $students->count() - $themes->count();
            $message .= ". Студентов без тем: {$remaining}";
        }

        return redirect()->route('dashboard')->with('success', $message);
    }
}
