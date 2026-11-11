@extends('layouts.app')

@section('title', 'Звернення ' . $request->number)
@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="mb-1">{{ $request->number }}</h1>
            <div class="text-muted">{{ $request->subject }}</div>
        </div>
        <span class="badge bg-secondary fs-6">{{ $request->status->label() }}</span>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Деталі</div>
                <div class="card-body">
                    <p><strong>Клієнт:</strong> {{ $request->client->name }} ({{ $request->client->email }})</p>
                    <p><strong>Послуга:</strong> {{ $request->service->name }}</p>
                    <p><strong>Контактна особа:</strong> {{ $request->contact_name }}</p>
                    <p><strong>Email:</strong> {{ $request->contact_email }}</p>
                    <p><strong>Телефон:</strong> {{ $request->contact_phone ?: '—' }}</p>
                    <hr>
                    <p class="text-break">{!! nl2br(e($request->body)) !!}</p>
                </div>
            </div>

            <h3 class="mt-4">Історія</h3>
            <div class="list-group">
                @foreach($request->histories as $h)
                    <div class="list-group-item {{ $h->is_internal ? 'bg-light' : '' }}">
                        <div class="d-flex justify-content-between">
                            <div>
                                @if($h->from_status && $h->from_status !== $h->to_status)
                                    <strong>{{ $h->from_status->label() }} → {{ $h->to_status->label() }}</strong>
                                @elseif($h->from_status === null)
                                    <strong>Створено</strong>
                                @else
                                    <strong>Коментар</strong>
                                @endif
                                — {{ $h->user->name }}
                                @if($h->is_internal)
                                    <span class="badge bg-warning text-dark">внутрішній</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $h->created_at->format('d.m.Y H:i') }}</small>
                        </div>
                        @if($h->comment)<p class="mb-0 mt-1">{{ $h->comment }}</p>@endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-md-4">
            @if(!$request->manager_id)
                <form method="POST" action="{{ route('manager.assign', $request) }}" class="mb-3">
                    @csrf
                    <button class="btn btn-outline-primary w-100">Призначити себе</button>
                </form>
            @else
                <div class="alert alert-secondary">Відповідальний: {{ $request->manager->name }}</div>
            @endif

            <div class="card mb-3">
                <div class="card-header">Змінити статус</div>
                <div class="card-body">
                    @if(empty($allowedNext))
                        <p class="text-muted mb-0">Статус термінальний, переходи недоступні.</p>
                    @else
                        <form method="POST" action="{{ route('manager.status', $request) }}">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select mb-2" required>
                                <option value="">— Оберіть —</option>
                                @foreach($allowedNext as $s)
                                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                                @endforeach
                            </select>
                            <textarea name="rejection_reason" class="form-control mb-2" rows="2"
                                      placeholder="Причина відхилення (обов'язково, якщо відхиляєте)"></textarea>
                            <textarea name="comment" class="form-control mb-2" rows="2"
                                      placeholder="Коментар (необов'язково)"></textarea>
                            <button class="btn btn-primary w-100">Зберегти</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">Додати коментар</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('manager.comment', $request) }}">
                        @csrf
                        <textarea name="comment" class="form-control mb-2" rows="3" required maxlength="2000"></textarea>
                        <label class="form-check mb-2">
                            <input type="checkbox" name="is_internal" value="1" class="form-check-input">
                            <span class="form-check-label">Внутрішній коментар</span>
                        </label>
                        <button class="btn btn-primary btn-sm">Надіслати</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
