<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    /**
     * Menampilkan daftar voucher.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $status = $request->input('status');
        $type = $request->input('type');

        $query = Voucher::query();

        // Search berdasarkan code atau nama voucher
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('code', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        // Filter status
        if ($status === 'active') {
            $query->where('is_active', true);
        }

        if ($status === 'inactive') {
            $query->where('is_active', false);
        }

        // Filter tipe diskon
        if (in_array($type, ['percentage', 'fixed'], true)) {
            $query->where('discount_type', $type);
        }

        $vouchers = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.vouchers.index',
            compact(
                'vouchers',
                'search',
                'status',
                'type'
            )
        );
    }


    /**
     * Menampilkan form tambah voucher.
     */
    public function create()
    {
        $voucher = null;

        return view(
            'admin.vouchers.form',
            compact('voucher')
        );
    }


    /**
     * Menyimpan voucher baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validateVoucher($request);

        Voucher::create([
            'code' => strtoupper(
                trim($validated['code'])
            ),

            'name' => $validated['name'],

            'discount_type' =>
                $validated['discount_type'],

            'discount_value' =>
                $validated['discount_value'],

            'min_order_amount' =>
                $validated['min_order_amount'] ?? 0,

            'max_discount_amount' =>
                $validated['max_discount_amount'] ?? null,

            'usage_limit' =>
                $validated['usage_limit'] ?? null,

            // Voucher baru belum pernah digunakan
            'used_count' => 0,

            'starts_at' =>
                $validated['starts_at'] ?? null,

            'expires_at' =>
                $validated['expires_at'] ?? null,

            'is_active' =>
                $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.vouchers.index')
            ->with(
                'success',
                'Voucher berhasil ditambahkan.'
            );
    }


    /**
     * Menampilkan form edit voucher.
     */
    public function edit(Voucher $voucher)
    {
        return view(
            'admin.vouchers.form',
            compact('voucher')
        );
    }


    /**
     * Memperbarui voucher.
     */
    public function update(
        Request $request,
        Voucher $voucher
    ) {
        $validated = $this->validateVoucher(
            $request,
            $voucher
        );

        $voucher->update([
            'code' => strtoupper(
                trim($validated['code'])
            ),

            'name' => $validated['name'],

            'discount_type' =>
                $validated['discount_type'],

            'discount_value' =>
                $validated['discount_value'],

            'min_order_amount' =>
                $validated['min_order_amount'] ?? 0,

            'max_discount_amount' =>
                $validated['max_discount_amount'] ?? null,

            'usage_limit' =>
                $validated['usage_limit'] ?? null,

            // used_count sengaja tidak diubah admin

            'starts_at' =>
                $validated['starts_at'] ?? null,

            'expires_at' =>
                $validated['expires_at'] ?? null,

            'is_active' =>
                $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.vouchers.index')
            ->with(
                'success',
                'Voucher berhasil diperbarui.'
            );
    }


    /**
     * Menghapus voucher.
     */
    public function destroy(Voucher $voucher)
    {
        // Voucher yang sudah digunakan pada order tidak boleh dihapus
        if ($voucher->orders()->exists()) {
            return redirect()
                ->route('admin.vouchers.index')
                ->with(
                    'error',
                    'Voucher tidak dapat dihapus karena sudah digunakan pada order.'
                );
        }

        $voucher->delete();

        return redirect()
            ->route('admin.vouchers.index')
            ->with(
                'success',
                'Voucher berhasil dihapus.'
            );
    }


    /**
     * Validasi data voucher.
     */
    private function validateVoucher(
        Request $request,
        ?Voucher $voucher = null
    ): array {
        $codeRule = Rule::unique(
            'vouchers',
            'code'
        );

        // Saat edit, code milik voucher sekarang boleh tetap digunakan
        if ($voucher) {
            $codeRule->ignore($voucher->id);
        }

        $rules = [

            'code' => [
                'required',
                'string',
                'max:50',
                $codeRule,
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'discount_type' => [
                'required',
                'in:percentage,fixed',
            ],

            'discount_value' => [
                'required',
                'numeric',
                'min:0',

                function (
                    $attribute,
                    $value,
                    $fail
                ) use ($request) {

                    // Diskon percentage maksimal 100%
                    if (
                        $request->input('discount_type')
                        === 'percentage'
                        && $value > 100
                    ) {
                        $fail(
                            'Percentage discount tidak boleh lebih dari 100%.'
                        );
                    }
                },
            ],

            'min_order_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'max_discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'usage_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],
        ];

        return $request->validate($rules);
    }
}