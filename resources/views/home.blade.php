@extends('layouts.app')

@section('title', 'Главная - Rinh Theme')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <!-- Hero Section -->
        <div class="jumbotron bg-primary text-white rounded p-5 mb-5">
            <h1 class="display-4"><i class="bi bi-journal-text"></i> Добро пожаловать в Rinh Theme</h1>
            <p class="lead">Система автоматического и ручного распределения тем курсовых работ среди студентов</p>
            <hr class="my-4 bg-white">
            <p>Администраторы могут загружать темы и списки студентов, система автоматически распределит темы, а старосты могут вручную корректировать распределение.</p>
            @if(!auth()->check())
                <a class="btn btn-light btn-lg" href="{{ route('login') }}" role="button">
                    <i class="bi bi-box-arrow-in-right"></i> Войти в систему
                </a>
            @endif
        </div>

        <!-- Features -->
        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-person-badge text-primary" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Для администраторов</h5>
                        <p class="card-text">Загружайте темы курсовых работ и списки студентов. Система автоматически распределит темы случайным образом.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-people text-success" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Для старост</h5>
                        <p class="card-text">Просматривайте распределение тем в вашей группе и при необходимости вручную закрепляйте темы за студентами.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-person text-info" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Для студентов</h5>
                        <p class="card-text">Просматривайте назначенную вам тему курсовой работы и получайте всю необходимую информацию.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- How it works -->
        <div class="card mb-5">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0"><i class="bi bi-question-circle"></i> Как это работает?</h4>
            </div>
            <div class="card-body">
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">
                        <strong>Администратор загружает данные:</strong> Загружает список тем курсовых работ и список студентов через удобный интерфейс.
                    </li>
                    <li class="list-group-item">
                        <strong>Автоматическое распределение:</strong> Система автоматически случайным образом распределяет темы среди студентов.
                    </li>
                    <li class="list-group-item">
                        <strong>Ручная корректировка:</strong> Староста группы может просмотреть распределение и при необходимости вручную закрепить тему за конкретным студентом.
                    </li>
                    <li class="list-group-item">
                        <strong>Просмотр результата:</strong> Студенты могут войти в систему и увидеть назначенную им тему курсовой работы.
                    </li>
                </ol>
            </div>
        </div>

        @if(auth()->check())
            <div class="text-center">
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-speedometer2"></i> Перейти в панель управления
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

