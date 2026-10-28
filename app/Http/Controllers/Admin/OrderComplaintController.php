<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderComplaintController extends Controller
{
    /**
     * Menampilkan daftar komplain pesanan.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => [
                'nullable',
                'in:open,investigating,resolved,rejected',
            ],
        ]);

        $query = OrderComplaint::with([
            'order',
            'user',
            'handledBy',
        ])->latest();

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $complaints = $query
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.complaints.index',
            compact('complaints')
        );
    }

    /**
     * Memperbarui penanganan komplain oleh Admin.
     */
    public function update(
        Request $request,
        OrderComplaint $complaint
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:open,investigating,resolved,rejected',
            ],
            'admin_response' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        DB::transaction(function () use (
            $complaint,
            $validated
        ) {
            $lockedComplaint = OrderComplaint::query()
                ->lockForUpdate()
                ->findOrFail($complaint->id);

            $closed = in_array(
                $validated['status'],
                ['resolved', 'rejected'],
                true
            );

            $lockedComplaint->update([
                'status' => $validated['status'],
                'admin_response' =>
                    $validated['admin_response'] ?? null,
                'handled_by' => auth()->id(),
                'resolved_at' => $closed ? now() : null,
            ]);
        });

        return back()->with(
            'success',
            'Penanganan komplain berhasil diperbarui.'
        );
    }
}