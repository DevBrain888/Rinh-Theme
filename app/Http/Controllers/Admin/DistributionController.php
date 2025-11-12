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
        // Получаем всех студентов без назначенных тем, группируем по группам
        $studentsWithoutThemes = Student::whereNull('theme_id')->get();
        
        if ($studentsWithoutThemes->isEmpty()) {
            return redirect()->route('dashboard')
                ->with('error', 'Нет студентов без назначенных тем. Загрузите студентов в разделе "Управление студентами".');
        }

        // Группируем студентов по группам
        $studentsByGroup = $studentsWithoutThemes->groupBy('group');
        
        $totalDistributed = 0;
        $messages = [];

        // Распределяем темы для каждой группы отдельно
        foreach ($studentsByGroup as $group => $students) {
            // Получаем доступные темы для этой группы (темы с этой группой или без группы)
            $availableThemes = Theme::where('status', 'available')
                ->whereNull('assigned_to')
                ->where(function($query) use ($group) {
                    $query->where('group', $group)
                          ->orWhereNull('group');
                })
                ->get();

            if ($availableThemes->isEmpty()) {
                $messages[] = "Группа {$group}: нет доступных тем";
                continue;
            }

            // Перемешиваем темы и студентов случайным образом
            $themes = $availableThemes->shuffle();
            $groupStudents = $students->shuffle();

            // Распределяем темы для этой группы
            $count = 0;
            $minCount = min($themes->count(), $groupStudents->count());

            for ($i = 0; $i < $minCount; $i++) {
                $theme = $themes[$i];
                $student = $groupStudents[$i];

                // Назначаем тему студенту
                $student->theme_id = $theme->id;
                $student->save();

                // Обновляем статус темы
                $theme->status = 'assigned';
                $theme->save();

                $count++;
                $totalDistributed++;
            }

            if ($count > 0) {
                $messages[] = "Группа {$group}: распределено {$count} тем";
            }
        }

        if ($totalDistributed === 0) {
            return redirect()->route('dashboard')
                ->with('error', 'Не удалось распределить темы. Убедитесь, что есть доступные темы для групп студентов.');
        }

        $message = "Успешно распределено тем: {$totalDistributed}. " . implode('; ', $messages);

        return redirect()->route('dashboard')->with('success', $message);
    }
}
