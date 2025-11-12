<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SupervisorController extends Controller
{
    public function index()
    {
        $supervisors = Supervisor::orderBy('name')->get();
        return view('admin.supervisors', compact('supervisors'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'supervisors_file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ], [
            'supervisors_file.required' => 'Пожалуйста, выберите файл для загрузки.',
            'supervisors_file.mimes' => 'Поддерживаются только форматы Excel (.xlsx, .xls) и CSV.',
        ]);

        $file = $request->file('supervisors_file');
        $extension = $file->getClientOriginalExtension();
        $supervisors = $this->processFile($file, $extension);

        if (empty($supervisors)) {
            $errorMessage = 'Не удалось извлечь данные руководителей из файла. ';
            if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                $errorMessage .= 'Проверьте содержимое Excel файла. Формат: первая колонка — ФИО, вторая — должность (опционально), третья — email (опционально).';
            } else {
                $errorMessage .= 'Проверьте формат CSV файла. Формат: ФИО, должность (опционально), email (опционально).';
            }
            return redirect()->route('admin.supervisors')->with('error', $errorMessage);
        }

        // Сохранение руководителей в базу данных
        $count = 0;
        foreach ($supervisors as $supervisorData) {
            if (!empty(trim($supervisorData['name']))) {
                Supervisor::create([
                    'name' => trim($supervisorData['name']),
                    'position' => isset($supervisorData['position']) && !empty($supervisorData['position']) ? trim($supervisorData['position']) : null,
                    'email' => isset($supervisorData['email']) && !empty($supervisorData['email']) ? trim($supervisorData['email']) : null,
                ]);
                $count++;
            }
        }

        return redirect()->route('admin.supervisors')
            ->with('success', "Успешно загружено руководителей: {$count}");
    }

    public function destroy($id)
    {
        $supervisor = Supervisor::findOrFail($id);
        $supervisor->delete();
        
        return response()->json(['success' => true]);
    }

    private function processFile($file, $extension)
    {
        $supervisors = [];

        switch (strtolower($extension)) {
            case 'csv':
                $supervisors = $this->processCsv($file);
                break;
            case 'xlsx':
            case 'xls':
                $supervisors = $this->processExcel($file);
                break;
        }

        return $supervisors;
    }

    private function processCsv($file)
    {
        $supervisors = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if (!empty($data[0])) {
                $supervisors[] = [
                    'name' => $data[0],
                    'position' => $data[1] ?? null,
                    'email' => $data[2] ?? null,
                ];
            }
        }
        fclose($handle);
        
        return $supervisors;
    }

    private function processExcel($file)
    {
        $supervisors = [];
        try {
            if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
                throw new \Exception('PhpSpreadsheet library not installed');
            }
            
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            foreach ($rows as $row) {
                if (!empty($row[0]) && is_string($row[0])) {
                    $supervisors[] = [
                        'name' => trim($row[0]),
                        'position' => isset($row[1]) && !empty($row[1]) ? trim($row[1]) : null,
                        'email' => isset($row[2]) && !empty($row[2]) ? trim($row[2]) : null,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Если библиотека не установлена, возвращаем пустой массив
        }
        
        return $supervisors;
    }
}
