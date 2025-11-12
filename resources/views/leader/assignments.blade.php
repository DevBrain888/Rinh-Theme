@extends('layouts.app')

@section('title', 'Распределение тем - Rinh Theme')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4"><i class="bi bi-list-check"></i> Распределение тем в группе</h1>
        @if(isset($leaderStudent))
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Вы работаете с группой: <strong>{{ $leaderStudent->group }}</strong>
            </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">Студенты и их темы</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Студент</th>
                        <th>Группа</th>
                        <th>Телефон</th>
                        <th>Тема</th>
                        <th>Руководитель</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students ?? [] as $student)
                        <tr>
                            <td><strong>{{ $student->name }}</strong></td>
                            <td>{{ $student->group }}</td>
                            <td>{{ $student->phone ?? '-' }}</td>
                            <td>
                                @if($student->theme)
                                    <span class="badge bg-success">{{ $student->theme->title }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($student->theme && $student->theme->supervisor)
                                    <span class="badge bg-primary">{{ $student->theme->supervisor->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($student->theme)
                                    <span class="badge bg-success">Назначена</span>
                                @else
                                    <span class="badge bg-secondary">Свободна</span>
                                @endif
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
@endsection
