<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = Theme::with('assignedUser')->orderBy('created_at', 'desc')->get();
        return view('admin.themes', compact('themes'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'themes_file' => 'nullable|file|mimes:csv,xlsx,xls|max:10240',
            'themes_text' => 'nullable|string|required_without:themes_file',
        ], [
            'themes_file.mimes' => 'Поддерживаются только форматы Excel (.xlsx, .xls) и CSV (согласно ТЗ).',
            'themes_text.required_without' => 'Пожалуйста, загрузите файл или введите темы в текстовое поле.',
        ]);

        $themes = [];

        // Обработка файла
        if ($request->hasFile('themes_file')) {
            $file = $request->file('themes_file');
            $extension = $file->getClientOriginalExtension();
            $themes = $this->processFile($file, $extension);
        } 
        // Обработка текстового поля
        elseif ($request->filled('themes_text')) {
            $themes = $this->processText($request->themes_text);
        } else {
            return redirect()->route('admin.themes')
                ->with('error', 'Пожалуйста, загрузите файл или введите темы в текстовое поле.');
        }

        if (empty($themes)) {
            $errorMessage = 'Не удалось извлечь темы из файла. ';
            if ($request->hasFile('themes_file')) {
                $extension = $request->file('themes_file')->getClientOriginalExtension();
                if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                    $errorMessage .= 'Для работы с Excel файлами установите: composer require phpoffice/phpspreadsheet';
                } else {
                    $errorMessage .= 'Проверьте формат CSV файла и убедитесь, что файл содержит темы. Формат: название темы в первой колонке, описание во второй (опционально).';
                }
            } else {
                $errorMessage .= 'Проверьте введенные данные.';
            }
            return redirect()->route('admin.themes')->with('error', $errorMessage);
        }

        // Сохранение тем в базу данных
        $count = 0;
        foreach ($themes as $themeData) {
            if (!empty(trim($themeData['title']))) {
                Theme::create([
                    'title' => trim($themeData['title']),
                    'description' => isset($themeData['description']) ? trim($themeData['description']) : null,
                    'status' => 'available',
                ]);
                $count++;
            }
        }

        return redirect()->route('admin.themes')
            ->with('success', "Успешно загружено тем: {$count}");
    }

    public function destroy($id)
    {
        $theme = Theme::findOrFail($id);
        $theme->delete();
        
        return response()->json(['success' => true]);
    }

    private function processFile($file, $extension)
    {
        $themes = [];

        switch (strtolower($extension)) {
            case 'csv':
                $themes = $this->processCsv($file);
                break;
            case 'xlsx':
            case 'xls':
                $themes = $this->processExcel($file);
                break;
            default:
                // Неподдерживаемый формат
                break;
        }

        return $themes;
    }

    private function processCsv($file)
    {
        $themes = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            if (!empty($data[0])) {
                $themes[] = [
                    'title' => $data[0],
                    'description' => $data[1] ?? null,
                ];
            }
        }
        fclose($handle);
        
        return $themes;
    }

    private function processExcel($file)
    {
        $themes = [];
        try {
            if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
                throw new \Exception('PhpSpreadsheet library not installed');
            }
            
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            foreach ($rows as $row) {
                if (!empty($row[0]) && is_string($row[0])) {
                    $themes[] = [
                        'title' => trim($row[0]),
                        'description' => isset($row[1]) && !empty($row[1]) ? trim($row[1]) : null,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Если библиотека не установлена, возвращаем пустой массив
            // Пользователю нужно установить: composer require phpoffice/phpspreadsheet
        }
        
        return $themes;
    }


    private function processText($text)
    {
        $themes = [];
        $lines = explode("\n", $text);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $themes[] = [
                    'title' => $line,
                    'description' => null,
                ];
            }
        }
        
        return $themes;
    }
}
