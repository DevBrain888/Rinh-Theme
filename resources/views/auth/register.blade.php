@extends('layouts.app')

@section('title', 'Регистрация - Rinh Theme')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0"><i class="bi bi-person-plus"></i> Регистрация</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               autofocus
                               placeholder="Введите ваше имя">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required
                               placeholder="example@mail.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required
                               placeholder="Минимум 8 символов">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required
                               placeholder="Повторите пароль">
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Роль</label>
                        <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" 
                                name="role" 
                                required>
                            <option value="">Выберите роль...</option>
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Студент</option>
                            <option value="group_leader" {{ old('role') == 'group_leader' ? 'selected' : '' }}>Староста</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Администратор</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Выберите вашу роль в системе</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-person-plus"></i> Зарегистрироваться
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <p class="mb-0">Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a></p>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('home') }}" class="text-muted text-decoration-none">
                <i class="bi bi-arrow-left"></i> Вернуться на главную
            </a>
        </div>
    </div>
</div>
@endsection

