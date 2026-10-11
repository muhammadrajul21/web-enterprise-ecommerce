<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profile customer.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return view(
            'customer.account',
            compact('user')
        );
    }


    /**
     * Update data profile customer.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',

                Rule::unique(
                    'users',
                    'email'
                )->ignore($user->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],
        ]);


        $user->update([
            'name' =>
                $validated['name'],

            'email' =>
                $validated['email'],

            'phone' =>
                $validated['phone']
                ?: null,
        ]);


        return redirect()
            ->route('customer.account')
            ->with(
                'success',
                'Profile berhasil diperbarui.'
            );
    }
}