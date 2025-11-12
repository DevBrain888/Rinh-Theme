<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('theme')->orderBy('created_at', 'desc')->get();
        return view('admin.students', compact('students'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'students_file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ], [
            'students_file.required' => 'Пожалуйста, выберите файл для загрузки.',
            'students_file.mimes' => 'Поддерживаются только форматы Excel (.xlsx, .xls) и CSV.',
        ]);

        $file = $request->file('students_file');
        $extension = $file->getClientOriginalExtension();
        $students = $this->processFile($file, $extension);

        if (empty($students)) {
            $errorMessage = 'Не удалось извлечь данные студентов из файла. ';
            if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                $errorMessage .= 'Проверьте содержимое Excel файла. Формат: первая колонка — ФИО, вторая — группа, третья — email (опционально).';
            } else {
                $errorMessage .= 'Проверьте формат CSV файла. Формат: ФИО, группа, email (опционально).';
            }
            return redirect()->route('admin.students')->with('error', $errorMessage);
        }

        // Сохранение студентов в базу данных
        $count = 0;
        foreach ($students as $studentData) {
            if (!empty(trim($studentData['name']))) {
                Student::create([
                    'name' => trim($studentData['name']),
                    'group' => trim($studentData['group'] ?? ''),
                    'email' => isset($studentData['email']) && !empty($studentData['email']) ? trim($studentData['email']) : null,
                ]);
                $count++;
            }
        }

        return redirect()->route('admin.students')
            ->with('success', "Успешно загружено студентов: {$count}");
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        
        return response()->json(['success' => true]);
    }

    private function processFile($file, $extension)
    {
        $students = [];

        switch (strtolower($extension)) {
            case 'csv':
                $students = $this->processCsv($file);
                break;
            case 'xlsx':
            case 'xls':
                $students = $this->processExcel($file);
                break;
            default:
                // Неподдерживаемый формат
                break;
        }

        return $students;
    }

    private function processCsv($file)
    {
        $students = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        // Пропускаем заголовок, если он есть
        $firstLine = fgetcsv($handle, 1000, ',');
        if ($firstLine && (strtolower($firstLine[0]) === 'фио' || strtolower($firstLine[0]) === 'name' || strtolower($firstLine[0]) === 'fio')) {
            // Это заголовок, пропускаем
        } else {
            // Это не заголовок, возвращаемся к началу
            rewind($handle);
        }
        
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if (!empty($data[0])) {
                $students[] = [
                    'name' => $data[0] ?? '',
                    'group' => $data[1] ?? '',
                    'email' => $data[2] ?? null,
                ];
            }
        }
        fclose($handle);
        
        return $students;
    }

    private function processExcel($file)
    {
        $students = [];
        try {
            if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
                throw new \Exception('PhpSpreadsheet library not installed');
            }
            
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Пропускаем заголовок, если он есть
            $startRow = 0;
            if (!empty($rows[0]) && is_string($rows[0][0])) {
                $firstCell = strtolower(trim($rows[0][0]));
                if (in_array($firstCell, ['фио', 'name', 'fio', 'фіо'])) {
                    $startRow = 1; // Пропускаем первую строку (заголовок)
                }
            }

            for ($i = $startRow; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (!empty($row[0]) && is_string($row[0])) {
                    $students[] = [
                        'name' => trim($row[0] ?? ''),
                        'group' => trim($row[1] ?? ''),
                        'email' => isset($row[2]) && !empty($row[2]) ? trim($row[2]) : null,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Если библиотека не установлена, возвращаем пустой массив
        }
        
        return $students;
    }
}
