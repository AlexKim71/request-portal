@extends('layouts.app')

@section('title', 'Каталог послуг')
@section('content')
    <h1 class="mb-3">Каталог послуг</h1>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                   placeholder="Пошук за назвою або описом">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Пошук</button>
            <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">Скинути</a>
        </div>
    </form>

    @if($services->isEmpty())
        <div class="alert alert-info">Послуг не знайдено.</div>
    @else
        <div class="row g-3">
            @foreach($services as $service)
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $service->name }}</h5>
                            <p class="card-text text-muted">{{ $service->description }}</p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('client-requests.create', ['service_id' => $service->id]) }}"
                               class="btn btn-sm btn-primary">Звернутись</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-3">{{ $services->links() }}</div>
    @endif
@endsection
