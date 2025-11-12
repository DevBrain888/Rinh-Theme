<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Theme;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    public function index()
    {
        // Получаем группу старосты через его email
        $leaderEmail = auth()->user()->email;
        $leaderStudent = Student::where('email', $leaderEmail)->first();
        
        if (!$leaderStudent) {
            return redirect()->route('dashboard')
                ->with('error', 'Староста не найден в списке студентов. Убедитесь, что ваш email совпадает с email в таблице студентов.');
        }
        
        // Фильтруем студентов по группе старосты
        $students = Student::where('group', $leaderStudent->group)
            ->with(['theme.supervisor'])
            ->orderBy('name')
            ->get();
        
        // Получаем темы для этой группы (темы с этой группой или без группы)
        $themes = Theme::where(function($query) use ($leaderStudent) {
                $query->where('group', $leaderStudent->group)
                      ->orWhereNull('group');
            })
            ->orderBy('title')
            ->get();
        
        return view('leader.assignments', compact('students', 'themes', 'leaderStudent'));
    }

    public function create()
    {
        // Получаем группу старосты через его email
        $leaderEmail = auth()->user()->email;
        $leaderStudent = Student::where('email', $leaderEmail)->first();
        
        if (!$leaderStudent) {
            return redirect()->route('dashboard')
                ->with('error', 'Староста не найден в списке студентов. Убедитесь, что ваш email совпадает с email в таблице студентов.');
        }
        
        // Очищаем истекшие блокировки перед получением тем
        $this->clearExpiredReservations();
        
        // ВАЖНО: Для тестирования установлено 10 секунд. Для продакшена измените на 30 минут (1800 секунд)
        // ИЗМЕНИТЬ ДЛЯ ПРОДАКШЕНА: замените 10 на 1800 (30 минут * 60 секунд)
        $reservationTimeoutSeconds = 10; // ТЕСТ: 10 секунд | ПРОДАКШЕН: 1800 секунд (30 минут)
        $expiredTime = Carbon::now()->subSeconds($reservationTimeoutSeconds);
        
        // Получаем свободные темы для группы старосты (темы с этой группой или без группы)
        // Исключаем темы, заблокированные другими группами (если блокировка не истекла)
        $availableThemes = Theme::where('status', 'available')
            ->where(function($query) use ($leaderStudent) {
                $query->where('group', $leaderStudent->group)
                      ->orWhereNull('group');
            })
            ->where(function($query) use ($leaderStudent, $expiredTime) {
                // Тема доступна, если:
                // 1. Она не заблокирована
                // 2. Заблокирована этой группой
                // 3. Заблокирована другой группой, но блокировка истекла (и руководитель не заполнен)
                $query->whereNull('reserved_by_group')
                      ->orWhere('reserved_by_group', $leaderStudent->group)
                      ->orWhere(function($q) use ($expiredTime) {
                          // Блокировка истекла и руководитель не заполнен
                          $q->where('reserved_at', '<', $expiredTime)
                            ->whereNull('supervisor_id');
                      });
            })
            ->orderBy('title')
            ->get();
        
        // Получаем студентов без тем из группы старосты
        $studentsWithoutThemes = Student::whereNull('theme_id')
            ->where('group', $leaderStudent->group)
            ->orderBy('name')
            ->get();
        
        // Получаем список всех руководителей для выбора
        $supervisors = Supervisor::orderBy('name')->get();
        
        return view('leader.assign', compact('availableThemes', 'studentsWithoutThemes', 'leaderStudent', 'supervisors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'theme_id' => 'required|exists:themes,id',
            'phone' => 'nullable|string|max:20',
            'supervisor_id' => 'nullable|exists:supervisors,id',
        ]);

        $student = Student::findOrFail($request->student_id);
        $theme = Theme::findOrFail($request->theme_id);
        
        // Проверяем, что староста может назначать темы только студентам своей группы
        $leaderEmail = auth()->user()->email;
        $leaderStudent = Student::where('email', $leaderEmail)->first();
        
        if (!$leaderStudent) {
            return redirect()->route('dashboard')
                ->with('error', 'Староста не найден в списке студентов.');
        }
        
        if ($student->group !== $leaderStudent->group) {
            return redirect()->route('leader.assign')
                ->with('error', 'Вы можете назначать темы только студентам своей группы.');
        }

        // Проверяем, что тема свободна
        if ($theme->status === 'assigned') {
            return redirect()->route('leader.assign')
                ->with('error', 'Эта тема уже назначена другому студенту.');
        }

        // Проверяем блокировку темы (блокировка на 30 минут)
        // ВАЖНО: Для тестирования установлено 10 секунд. Для продакшена измените на 30 минут (1800 секунд)
        // ИЗМЕНИТЬ ДЛЯ ПРОДАКШЕНА: замените 10 на 1800 (30 минут * 60 секунд)
        $reservationTimeoutSeconds = 10; // ТЕСТ: 10 секунд | ПРОДАКШЕН: 1800 секунд (30 минут)
        
        if ($theme->reserved_by_group && $theme->reserved_by_group !== $leaderStudent->group) {
            // Проверяем, не истекла ли блокировка
            if ($theme->reserved_at) {
                $reservationExpiryTime = $theme->reserved_at->copy()->addSeconds($reservationTimeoutSeconds);
                if ($reservationExpiryTime->isFuture()) {
                    return redirect()->route('leader.assign')
                        ->with('error', 'Эта тема временно заблокирована другой группой. Попробуйте позже.');
                } else {
                    // Блокировка истекла, очищаем её
                    $theme->reserved_by_group = null;
                    $theme->reserved_at = null;
                }
            } else {
                // Если нет времени блокировки, но есть группа - очищаем
                $theme->reserved_by_group = null;
            }
        }

        // Блокируем тему для этой группы
        $theme->reserved_by_group = $leaderStudent->group;
        $theme->reserved_at = Carbon::now();
        
        // Если выбран руководитель, сохраняем его
        if ($request->filled('supervisor_id')) {
            $theme->supervisor_id = $request->supervisor_id;
        }
        
        $theme->save();

        // Назначаем тему студенту
        $student->theme_id = $theme->id;
        if ($request->filled('phone')) {
            $student->phone = $request->phone;
        }
        $student->save();

        // Обновляем статус темы на "назначена" только если все данные заполнены
        // Если руководитель заполнен, тема считается назначенной
        if ($theme->supervisor_id) {
            $theme->status = 'assigned';
            // Очищаем блокировку, так как тема назначена
            $theme->reserved_by_group = null;
            $theme->reserved_at = null;
            $theme->save();
        }

        return redirect()->route('leader.assignments')
            ->with('success', 'Тема успешно назначена студенту!');
    }

    /**
     * Очистка истекших блокировок тем
     * Если поля не заполнены в течение 30 минут (кроме научного руководителя),
     * данные очищаются и тема становится доступной
     * 
     * ВАЖНО: Для тестирования установлено 10 секунд. Для продакшена измените на 30 минут (1800 секунд)
     * ИЗМЕНИТЬ ДЛЯ ПРОДАКШЕНА: замените 10 на 1800 (30 минут * 60 секунд)
     */
    private function clearExpiredReservations()
    {
        // ТЕСТ: 10 секунд | ПРОДАКШЕН: 1800 секунд (30 минут)
        $reservationTimeoutSeconds = 10; // ИЗМЕНИТЬ ДЛЯ ПРОДАКШЕНА: 10 -> 1800
        
        $expiredTime = Carbon::now()->subSeconds($reservationTimeoutSeconds);
        
        // Очищаем блокировки, где:
        // 1. Время блокировки истекло
        // 2. Научный руководитель НЕ заполнен (если руководитель заполнен, блокировка не снимается)
        Theme::whereNotNull('reserved_at')
            ->where('reserved_at', '<', $expiredTime)
            ->whereNull('supervisor_id')
            ->update([
                'reserved_by_group' => null,
                'reserved_at' => null,
            ]);
    }
}
