@extends('layouts.app')

@section('title', 'Панель управления - Rinh Theme')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4"><i class="bi bi-speedometer2"></i> Панель управления</h1>
    </div>
</div>

<div class="row">
    <!-- Admin Panel -->
    @if(auth()->check() && ((auth()->user()->role ?? null) === 'admin' || (auth()->user()->role ?? null) === 'administrator'))
    <div class="col-md-6 mb-4">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-shield-check"></i> Панель администратора</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Управление темами и студентами</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.themes') }}" class="btn btn-primary">
                        <i class="bi bi-file-earmark-text"></i> Управление темами
                    </a>
                    <a href="{{ route('admin.students') }}" class="btn btn-primary">
                        <i class="bi bi-people"></i> Управление студентами
                    </a>
                    <a href="{{ route('admin.supervisors') }}" class="btn btn-primary">
                        <i class="bi bi-person-badge"></i> Управление руководителями
                    </a>
                    <form action="{{ route('admin.distribute') }}" method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Вы уверены, что хотите распределить темы автоматически? Это назначит темы всем студентам случайным образом.');">
                            <i class="bi bi-shuffle"></i> Распределить темы автоматически
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Group Leader Panel -->
    @if(auth()->check() && ((auth()->user()->role ?? null) === 'group_leader' || (auth()->user()->role ?? null) === 'староста'))
    <div class="col-md-6 mb-4">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Панель старосты</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Управление распределением тем в вашей группе</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('leader.assignments') }}" class="btn btn-success">
                        <i class="bi bi-list-check"></i> Просмотр распределения
                    </a>
                    <a href="{{ route('leader.assign') }}" class="btn btn-success">
                        <i class="bi bi-pencil-square"></i> Назначить темы вручную
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Student Panel -->
    @if(auth()->check() && ((auth()->user()->role ?? null) === 'student' || (auth()->user()->role ?? null) === 'студент'))
    <div class="col-md-6 mb-4">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-person"></i> Панель студента</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Просмотр назначенной темы</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('student.theme') }}" class="btn btn-info">
                        <i class="bi bi-eye"></i> Моя тема курсовой работы
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Statistics -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Статистика</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="p-3 bg-primary text-white rounded">
                            <h3>0</h3>
                            <p class="mb-0">Тем</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-success text-white rounded">
                            <h3>0</h3>
                            <p class="mb-0">Студентов</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-info text-white rounded">
                            <h3>0</h3>
                            <p class="mb-0">Распределено</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-warning text-white rounded">
                            <h3>0</h3>
                            <p class="mb-0">Ожидает</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

