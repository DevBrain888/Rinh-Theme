@extends('layouts.app')

@section('title', 'Управление темами - Rinh Theme')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-file-earmark-text"></i> Управление темами</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-upload"></i> Загрузить темы
            </button>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Загрузка тем</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.themes.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="themes_file" class="form-label">Выберите файл с темами (необязательно)</label>
                        <input type="file" class="form-control" id="themes_file" name="themes_file" accept=".csv,.xlsx,.xls">
                        <div class="form-text">
                            <strong>Поддерживаемые форматы:</strong> Excel (.xlsx, .xls) или CSV (согласно ТЗ)
                            <br><strong>Формат файла:</strong> первая колонка - название темы, вторая колонка - описание (опционально)
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Или введите темы вручную (по одной на строку)</label>
                        <textarea class="form-control" id="themes_text" name="themes_text" rows="5" placeholder="Тема 1&#10;Тема 2&#10;Тема 3"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Примечание:</strong> Заполните либо файл, либо текстовое поле. Если заполнены оба, приоритет у файла.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Загрузить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Themes List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Список тем</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название темы</th>
                        <th>Описание</th>
                        <th>Статус</th>
                        <th>Назначена</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($themes ?? [] as $theme)
                        <tr>
                            <td>{{ $theme->id }}</td>
                            <td><strong>{{ $theme->title }}</strong></td>
                            <td>{{ $theme->description ?? '-' }}</td>
                            <td>
                                @if($theme->status === 'assigned')
                                    <span class="badge bg-success">Назначена</span>
                                @else
                                    <span class="badge bg-secondary">Доступна</span>
                                @endif
                            </td>
                            <td>
                                @if($theme->assignedUser)
                                    {{ $theme->assignedUser->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="deleteTheme({{ $theme->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="bi bi-inbox"></i> Темы еще не загружены
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
    function deleteTheme(id) {
        if (confirm('Вы уверены, что хотите удалить эту тему?')) {
            fetch(`/admin/themes/${id}`, {
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

