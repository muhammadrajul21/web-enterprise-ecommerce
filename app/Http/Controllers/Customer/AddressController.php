<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Menampilkan semua alamat customer.
     */
    public function index()
    {
        $addresses = Address::where(
            'user_id',
            auth()->id()
        )
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view(
            'customer.addresses.index',
            compact('addresses')
        );
    }


    /**
     * Menyimpan alamat baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => [
                'nullable',
                'string',
                'max:50',
            ],

            'recipient_name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'address' => [
                'required',
                'string',
            ],

            'city' => [
                'required',
                'string',
                'max:100',
            ],

            'province' => [
                'required',
                'string',
                'max:100',
            ],

            'postal_code' => [
                'required',
                'string',
                'max:10',
            ],
        ]);


        DB::transaction(function () use (
            $request,
            $validated
        ) {

            $hasAddress = Address::where(
                'user_id',
                auth()->id()
            )->exists();


            /*
             * Alamat pertama otomatis menjadi default.
             * Alamat berikutnya bisa dijadikan default
             * melalui checkbox.
             */
            $isDefault =
                !$hasAddress
                || $request->boolean('is_default');


            if ($isDefault) {

                Address::where(
                    'user_id',
                    auth()->id()
                )->update([
                    'is_default' => false,
                ]);

            }


            Address::create([
                'user_id' =>
                    auth()->id(),

                'label' =>
                    $validated['label']
                    ?: null,

                'recipient_name' =>
                    $validated['recipient_name'],

                'phone' =>
                    $validated['phone'],

                'address' =>
                    $validated['address'],

                'city' =>
                    $validated['city'],

                'province' =>
                    $validated['province'],

                'postal_code' =>
                    $validated['postal_code'],

                'is_default' =>
                    $isDefault,
            ]);

        });


        return redirect()
            ->route('customer.addresses.index')
            ->with(
                'success',
                'Alamat berhasil ditambahkan.'
            );
    }


    /**
     * Update alamat.
     */
    public function update(
        Request $request,
        Address $address
    ) {
        $this->ensureOwner($address);


        $validated = $request->validate([
            'label' => [
                'nullable',
                'string',
                'max:50',
            ],

            'recipient_name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'address' => [
                'required',
                'string',
            ],

            'city' => [
                'required',
                'string',
                'max:100',
            ],

            'province' => [
                'required',
                'string',
                'max:100',
            ],

            'postal_code' => [
                'required',
                'string',
                'max:10',
            ],
        ]);


        DB::transaction(function () use (
            $request,
            $validated,
            $address
        ) {

            /*
             * Jika alamat yang sedang diedit sudah default,
             * tetap pertahankan sebagai default.
             */
            $isDefault =
                $address->is_default
                || $request->boolean('is_default');


            if ($isDefault) {

                Address::where(
                    'user_id',
                    auth()->id()
                )
                    ->where(
                        'id',
                        '!=',
                        $address->id
                    )
                    ->update([
                        'is_default' => false,
                    ]);

            }


            $address->update([
                'label' =>
                    $validated['label']
                    ?: null,

                'recipient_name' =>
                    $validated['recipient_name'],

                'phone' =>
                    $validated['phone'],

                'address' =>
                    $validated['address'],

                'city' =>
                    $validated['city'],

                'province' =>
                    $validated['province'],

                'postal_code' =>
                    $validated['postal_code'],

                'is_default' =>
                    $isDefault,
            ]);

        });


        return redirect()
            ->route('customer.addresses.index')
            ->with(
                'success',
                'Alamat berhasil diperbarui.'
            );
    }


    /**
     * Menjadikan alamat sebagai default.
     */
    public function setDefault(Address $address)
    {
        $this->ensureOwner($address);


        DB::transaction(function () use ($address) {

            Address::where(
                'user_id',
                auth()->id()
            )->update([
                'is_default' => false,
            ]);


            $address->update([
                'is_default' => true,
            ]);

        });


        return redirect()
            ->route('customer.addresses.index')
            ->with(
                'success',
                'Alamat default berhasil diubah.'
            );
    }


    /**
     * Menghapus alamat.
     */
    public function destroy(Address $address)
    {
        $this->ensureOwner($address);


        DB::transaction(function () use ($address) {

            $wasDefault =
                $address->is_default;


            $address->delete();


            /*
             * Jika alamat default dihapus,
             * jadikan alamat lain sebagai default.
             */
            if ($wasDefault) {

                $replacement = Address::where(
                    'user_id',
                    auth()->id()
                )
                    ->latest()
                    ->first();


                if ($replacement) {

                    $replacement->update([
                        'is_default' => true,
                    ]);

                }

            }

        });


        return redirect()
            ->route('customer.addresses.index')
            ->with(
                'success',
                'Alamat berhasil dihapus.'
            );
    }


    /**
     * Memastikan alamat milik customer login.
     */
    private function ensureOwner(Address $address): void
    {
        abort_unless(
            (int) $address->user_id
            === (int) auth()->id(),
            403
        );
    }
}