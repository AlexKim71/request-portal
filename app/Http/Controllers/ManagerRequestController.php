<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Http\Requests\UpdateRequestStatus;
use App\Models\ClientRequest;
use App\Models\RequestHistory;
use App\Services\RequestStatusTransition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ManagerRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientRequest::with(['service', 'client']);

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('client', fn ($q2) => $q2->where('email', 'like', "%{$search}%"));
            });
        }

        if ($date = $request->string('date')->trim()->toString()) {
            $query->whereDate('created_at', $date);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('manager.index', compact('requests'));
    }

    public function show(ClientRequest $clientRequest)
    {
        $clientRequest->load(['service', 'client', 'manager', 'histories.user']);

        return view('manager.show', [
            'request'     => $clientRequest,
            'allowedNext' => RequestStatusTransition::allowedNext($clientRequest->status),
        ]);
    }

    public function assign(Request $request, ClientRequest $clientRequest)
    {
        $clientRequest->update(['manager_id' => $request->user()->id]);

        return back()->with('status', 'Вас призначено відповідальним.');
    }

    public function updateStatus(UpdateRequestStatus $request, ClientRequest $clientRequest)
    {
        $data      = $request->validated();
        $newStatus = RequestStatus::from($data['status']);

        if (!RequestStatusTransition::canTransition($clientRequest->status, $newStatus)) {
            throw ValidationException::withMessages([
                'status' => "Перехід зі статусу «{$clientRequest->status->label()}» у «{$newStatus->label()}» не дозволено.",
            ]);
        }

        DB::transaction(function () use ($request, $clientRequest, $newStatus, $data) {
            $old = $clientRequest->status;
            $clientRequest->update([
                'status'           => $newStatus,
                'rejection_reason' => $newStatus === RequestStatus::REJECTED ? $data['rejection_reason'] : null,
                'manager_id'       => $clientRequest->manager_id ?? $request->user()->id,
            ]);

            RequestHistory::create([
                'client_request_id' => $clientRequest->id,
                'user_id'           => $request->user()->id,
                'from_status'       => $old,
                'to_status'         => $newStatus,
                'is_internal'       => false,
                'comment'           => $data['comment'] ?? null,
            ]);
        });

        return back()->with('status', 'Статус оновлено.');
    }
}
