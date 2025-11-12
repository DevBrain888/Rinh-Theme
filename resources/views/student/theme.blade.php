@extends('layouts.app')

@section('title', 'Моя тема - Rinh Theme')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4"><i class="bi bi-journal-text"></i> Моя тема курсовой работы</h1>
    </div>
</div>

<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Информация о теме</h5>
    </div>
    <div class="card-body">
        @if($student && $student->theme)
            <h5>Название темы:</h5>
            <p class="lead">{{ $student->theme->title }}</p>
            
            @if($student->theme->description)
                <h5>Описание:</h5>
                <p>{{ $student->theme->description }}</p>
            @endif
            
            @if($student->theme->supervisor)
                <h5>Научный руководитель:</h5>
                <p>
                    <strong>{{ $student->theme->supervisor->name }}</strong>
                    @if($student->theme->supervisor->position)
                        <br><small class="text-muted">{{ $student->theme->supervisor->position }}</small>
                    @endif
                    @if($student->theme->supervisor->email)
                        <br><small class="text-muted"><i class="bi bi-envelope"></i> {{ $student->theme->supervisor->email }}</small>
                    @endif
                </p>
            @else
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle"></i> Научный руководитель еще не назначен
                </div>
            @endif
            
            <div class="mt-4">
                <span class="badge bg-success">Назначена</span>
                @if($student->theme->created_at)
                    <span class="badge bg-secondary ms-2">Дата назначения: {{ $student->theme->created_at->format('d.m.Y') }}</span>
                @endif
            </div>
        @else
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle"></i> 
                <strong>Внимание!</strong> Вам еще не назначена тема курсовой работы. Обратитесь к старосте или администратору.
            </div>
        @endif
    </div>
</div>
@endsection
