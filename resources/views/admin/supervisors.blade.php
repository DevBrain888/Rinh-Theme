@extends('layouts.app')

@section('title', 'Управление руководителями - Rinh Theme')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-person-badge"></i> Управление руководителями</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-upload"></i> Загрузить руководителей
            </button>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Загрузка руководителей</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.supervisors.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="supervisors_file" class="form-label">Выберите файл с руководителями</label>
                        <input type="file" class="form-control" id="supervisors_file" name="supervisors_file" accept=".csv,.xlsx,.xls" required>
                        <div class="form-text">
                            <strong>Поддерживаемые форматы:</strong> Excel (.xlsx, .xls) или CSV
                            <br><strong>Формат файла:</strong> первая колонка - ФИО, вторая - должность (опционально), третья - email (опционально)
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

<!-- Supervisors List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Список руководителей</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ФИО</th>
                        <th>Должность</th>
                        <th>Email</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supervisors ?? [] as $supervisor)
                        <tr>
                            <td>{{ $supervisor->id }}</td>
                            <td><strong>{{ $supervisor->name }}</strong></td>
                            <td>{{ $supervisor->position ?? '-' }}</td>
                            <td>{{ $supervisor->email ?? '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="deleteSupervisor({{ $supervisor->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                <i class="bi bi-inbox"></i> Руководители еще не загружены
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
    function deleteSupervisor(id) {
        if (confirm('Вы уверены, что хотите удалить этого руководителя?')) {
            fetch(`/admin/supervisors/${id}`, {
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

