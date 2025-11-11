@extends('layouts.app')

@section('title', 'Управление студентами - Rinh Theme')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-people"></i> Управление студентами</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-upload"></i> Загрузить студентов
            </button>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Загрузка студентов</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.students.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="students_file" class="form-label">Выберите файл со студентами</label>
                        <input type="file" class="form-control" id="students_file" name="students_file" accept=".csv,.xlsx,.xls" required>
                        <div class="form-text">
                            <strong>Поддерживаемые форматы:</strong> Excel (.xlsx, .xls) или CSV (согласно ТЗ)
                            <br><strong>Формат файла:</strong> первая колонка - ФИО, вторая - группа, третья - email (опционально)
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Загрузить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Students List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Список студентов</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ФИО</th>
                        <th>Группа</th>
                        <th>Email</th>
                        <th>Тема</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students ?? [] as $student)
                        <tr>
                            <td>{{ $student->id }}</td>
                            <td><strong>{{ $student->name }}</strong></td>
                            <td>{{ $student->group }}</td>
                            <td>{{ $student->email ?? '-' }}</td>
                            <td>
                                @if($student->theme)
                                    <span class="badge bg-success">{{ $student->theme->title }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="deleteStudent({{ $student->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="bi bi-inbox"></i> Студенты еще не загружены
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deleteStudent(id) {
        if (confirm('Вы уверены, что хотите удалить этого студента?')) {
            fetch(`/admin/students/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                location.reload();
            });
        }
    }
</script>
@endpush
@endsection

