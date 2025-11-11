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
