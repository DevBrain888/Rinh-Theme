@extends('layouts.app')

@section('title', 'Назначение тем - Rinh Theme')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4"><i class="bi bi-pencil-square"></i> Назначение тем вручную</h1>
    </div>
</div>

<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">Выберите студента и тему (согласно ТЗ: с указанием номера телефона)</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('leader.assign.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="student_id" class="form-label">Студент</label>
                    <select class="form-select" id="student_id" name="student_id" required>
                        <option value="">Выберите студента...</option>
                        @foreach($studentsWithoutThemes ?? [] as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->group }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="theme_id" class="form-label">Тема</label>
                    <select class="form-select" id="theme_id" name="theme_id" required>
                        <option value="">Выберите тему...</option>
                        @foreach($availableThemes ?? [] as $theme)
                            <option value="{{ $theme->id }}">{{ $theme->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Номер телефона студента (согласно ТЗ)</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="+7 (999) 123-45-67">
                    <div class="form-text">Укажите номер телефона студента при назначении темы</div>
                </div>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Назначить тему
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
