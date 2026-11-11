@extends('layouts.app')

@section('title', 'Керування зверненнями')
@section('content')
    <h1 class="mb-3">Усі звернення</h1>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                   placeholder="Номер, тема або email">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">Усі статуси</option>
                @foreach(\App\Enums\RequestStatus::cases() as $s)
                    <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" name="date" value="{{ request('date') }}" class="form-control">
        </div>
        <div class="col-auto"><button class="btn btn-primary">Фільтрувати</button></div>
    </form>

    @if($requests->isEmpty())
        <div class="alert alert-info">Звернень не знайдено.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>Номер</th><th>Тема</th><th>Клієнт</th><th>Послуга</th>
                    <th>Статус</th><th>Створено</th><th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($requests as $req)
                    <tr>
                        <td>{{ $req->number }}</td>
                        <td>{{ $req->subject }}</td>
                        <td>{{ $req->client->name }}<br>
                            <small class="text-muted">{{ $req->client->email }}</small>
                        </td>
                        <td>{{ $req->service->name }}</td>
                        <td><span class="badge bg-secondary">{{ $req->status->label() }}</span></td>
                        <td>{{ $req->created_at->format('d.m.Y H:i') }}</td>
                        <td><a href="{{ route('manager.show', $req) }}" class="btn btn-sm btn-outline-primary">Відкрити</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $requests->links() }}</div>
    @endif
@endsection
