@extends('layouts.app')

@section('title', 'Мої звернення')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Мої звернення</h1>
        <a href="{{ route('client-requests.create') }}" class="btn btn-primary">Нове звернення</a>
    </div>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Пошук за номером">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Усі статуси</option>
                @foreach(\App\Enums\RequestStatus::cases() as $s)
                    <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary">Фільтрувати</button></div>
    </form>

    @if($requests->isEmpty())
        <div class="alert alert-info">Звернень не знайдено.</div>
    @else
        <div class="list-group">
            @foreach($requests as $req)
                <a href="{{ route('client-requests.show', $req) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $req->number }}</strong> — {{ $req->subject }}
                            <div class="small text-muted">
                                {{ $req->service->name }} · {{ $req->created_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                        <span class="badge bg-secondary align-self-start">{{ $req->status->label() }}</span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-3">{{ $requests->links() }}</div>
    @endif
@endsection
