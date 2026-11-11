@extends('layouts.app')

@section('title', 'Нове звернення')
@section('content')
    <h1 class="mb-3">Нове звернення</h1>

    <form method="POST" action="{{ route('client-requests.store') }}" class="row g-3">
        @csrf
        <div class="col-md-6">
            <label class="form-label">Послуга</label>
            <select name="service_id" class="form-select" required>
                <option value="">— Оберіть —</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}"
                        @selected(old('service_id', request('service_id')) == $service->id)>{{ $service->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Тема</label>
            <input type="text" name="subject" value="{{ old('subject') }}" class="form-control"
                   required minlength="5" maxlength="200">
        </div>
        <div class="col-12">
            <label class="form-label">Опис</label>
            <textarea name="body" class="form-control" rows="5" required minlength="20"
                      maxlength="5000">{{ old('body') }}</textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label">Контактна особа</label>
            <input type="text" name="contact_name" value="{{ old('contact_name', auth()->user()->name) }}"
                   class="form-control" required maxlength="100">
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" name="contact_email" value="{{ old('contact_email', auth()->user()->email) }}"
                   class="form-control" required maxlength="150">
        </div>
        <div class="col-md-4">
            <label class="form-label">Телефон</label>
            <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                   class="form-control" maxlength="30">
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Створити звернення</button>
            <a href="{{ route('client-requests.index') }}" class="btn btn-outline-secondary">Скасувати</a>
        </div>
    </form>
@endsection
