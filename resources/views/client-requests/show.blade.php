@extends('layouts.app')

@section('title', 'Звернення ' . $request->number)
@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="mb-1">Звернення {{ $request->number }}</h1>
            <div class="text-muted">{{ $request->subject }}</div>
        </div>
        <span class="badge bg-secondary fs-6">{{ $request->status->label() }}</span>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Деталі</div>
                <div class="card-body">
                    <p><strong>Послуга:</strong> {{ $request->service->name }}</p>
                    <p><strong>Створено:</strong> {{ $request->created_at->format('d.m.Y H:i') }}</p>
                    <p><strong>Опис:</strong></p>
                    <p class="text-break">{!! nl2br(e($request->body)) !!}</p>
                    <hr>
                    <p><strong>Контактна особа:</strong> {{ $request->contact_name }}</p>
                    <p><strong>Email:</strong> {{ $request->contact_email }}</p>
                    <p><strong>Телефон:</strong> {{ $request->contact_phone ?: '—' }}</p>
                    @if($request->manager)
                        <p><strong>Відповідальний:</strong> {{ $request->manager->name }}</p>
                    @endif
                    @if($request->rejection_reason)
                        <div class="alert alert-warning mt-3">
                            <strong>Причина відхилення:</strong> {{ $request->rejection_reason }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @can('cancel', $request)
                <form method="POST" action="{{ route('client-requests.cancel', $request) }}" class="mb-3"
                      onsubmit="return confirm('Скасувати звернення?')">
                    @csrf
                    <button class="btn btn-outline-danger w-100">Скасувати звернення</button>
                </form>
            @endcan

            <div class="card">
                <div class="card-header">Додати коментар</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('client-requests.comment', $request) }}">
                        @csrf
                        <textarea name="comment" class="form-control mb-2" rows="3" required maxlength="2000"></textarea>
                        <button class="btn btn-primary btn-sm">Надіслати</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-4">Історія</h3>
    <div class="list-group">
        @foreach($request->publicHistories as $h)
            <div class="list-group-item">
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
                    </div>
                    <small class="text-muted">{{ $h->created_at->format('d.m.Y H:i') }}</small>
                </div>
                @if($h->comment)<p class="mb-0 mt-1">{{ $h->comment }}</p>@endif
            </div>
        @endforeach
    </div>
@endsection
