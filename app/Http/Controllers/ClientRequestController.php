<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\StoreComment;
use App\Models\ClientRequest;
use App\Models\RequestHistory;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ClientRequestController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', ClientRequest::class);

        $query = ClientRequest::with('service')
            ->where('client_id', $request->user()->id);

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where('number', 'like', "%{$search}%");
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        return view('client-requests.index', compact('requests'));
    }

    public function create()
    {
        Gate::authorize('create', ClientRequest::class);
        $services = Service::active()->orderBy('name')->get();
        return view('client-requests.create', compact('services'));
    }

    public function store(StoreClientRequest $request)
    {
        $data = $request->validated();
        $data['client_id'] = $request->user()->id;
        $data['status']    = RequestStatus::NEW;

        $clientRequest = DB::transaction(function () use ($data) {
            $req = ClientRequest::create($data);

            RequestHistory::create([
                'client_request_id' => $req->id,
                'user_id'           => $data['client_id'],
                'from_status'       => null,
                'to_status'         => RequestStatus::NEW,
                'is_internal'       => false,
                'comment'           => 'Звернення створено.',
            ]);

            return $req;
        });

        return redirect()
            ->route('client-requests.show', $clientRequest)
            ->with('status', 'Звернення успішно створено. Номер: ' . $clientRequest->number);
    }

    public function show(ClientRequest $clientRequest)
    {
        Gate::authorize('view', $clientRequest);
        $clientRequest->load(['service', 'histories.user', 'manager']);

        return view('client-requests.show', ['request' => $clientRequest]);
    }

    public function cancel(Request $request, ClientRequest $clientRequest)
    {
        Gate::authorize('cancel', $clientRequest);

        DB::transaction(function () use ($request, $clientRequest) {
            $old = $clientRequest->status;
            $clientRequest->update([
                'status'           => RequestStatus::REJECTED,
                'rejection_reason' => 'Скасовано користувачем.',
            ]);
            RequestHistory::create([
                'client_request_id' => $clientRequest->id,
                'user_id'           => $request->user()->id,
                'from_status'       => $old,
                'to_status'         => RequestStatus::REJECTED,
                'is_internal'       => false,
                'comment'           => 'Звернення скасовано.',
            ]);
        });

        return back()->with('status', 'Звернення скасовано.');
    }

    public function comment(StoreComment $request, ClientRequest $clientRequest)
    {
        Gate::authorize('view', $clientRequest);

        $isInternal = $request->user()->isManager() && (bool) $request->input('is_internal', false);

        RequestHistory::create([
            'client_request_id' => $clientRequest->id,
            'user_id'           => $request->user()->id,
            'from_status'       => $clientRequest->status,
            'to_status'         => $clientRequest->status,
            'is_internal'       => $isInternal,
            'comment'           => $request->validated('comment'),
        ]);

        return back()->with('status', 'Коментар додано.');
    }
}
