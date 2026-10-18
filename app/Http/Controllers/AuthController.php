<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([

            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
            ],

        ]);


        $user = User::where(
            'email',
            $credentials['email']
        )->first();


        if (
            !$user
            || !Hash::check(
                $credentials['password'],
                $user->password
            )
        ) {

            return back()
                ->withErrors([
                    'email' =>
                        'Email atau password salah.',
                ])
                ->onlyInput('email');
        }


        if ($user->status !== 'active') {

            return back()
                ->withErrors([
                    'email' =>
                        'Akun Anda sedang tidak aktif.',
                ])
                ->onlyInput('email');
        }


        Auth::login(
            $user,
            $request->boolean('remember')
        );


        $request
            ->session()
            ->regenerate();


        /*
         * Jika customer sebelumnya
         * berbelanja sebagai guest,
         * pindahkan guest cart ke
         * database cart customer.
         */
        if (
            $user->role?->name
            === 'customer'
        ) {

            $this->mergeGuestCart(
                $request,
                $user
            );
        }


        return $this->redirectByRole(
            $user
        );
    }


    public function showRegister()
    {
        return view('auth.register');
    }


    public function register(
        Request $request
    ) {
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
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'min:8',
                'confirmed',
            ],

        ]);


        $customerRole = Role::where(
            'name',
            'customer'
        )
            ->firstOrFail();


        $user = User::create([

            'role_id' =>
                $customerRole->id,

            'name' =>
                $validated['name'],

            'email' =>
                $validated['email'],

            'phone' =>
                $validated['phone']
                ?? null,

            'status' =>
                'active',

            'password' =>
                Hash::make(
                    $validated['password']
                ),

        ]);


        Auth::login($user);


        $request
            ->session()
            ->regenerate();


        /*
         * Guest yang register setelah
         * belanja tetap membawa cart.
         */
        $this->mergeGuestCart(
            $request,
            $user
        );


        /*
         * Jika sebelumnya mencoba
         * membuka checkout, kembali
         * langsung ke checkout.
         */
        return redirect()->intended(
            route(
                'customer.account'
            )
        );
    }


    public function logout(
        Request $request
    ) {
        Auth::logout();


        $request
            ->session()
            ->invalidate();


        $request
            ->session()
            ->regenerateToken();


        return redirect()
            ->route('login');
    }


    /**
     * Menggabungkan guest cart
     * dengan cart customer.
     */
    private function mergeGuestCart(
        Request $request,
        User $user
    ): void {
        $guestCart = $request
            ->session()
            ->get(
                'guest_cart',
                []
            );


        if (empty($guestCart)) {
            return;
        }


        DB::transaction(
            function () use (
                $guestCart,
                $user
            ) {

                $cart = Cart::firstOrCreate([
                    'user_id' =>
                        $user->id,
                ]);


                foreach (
                    $guestCart
                    as $variantId => $quantity
                ) {

                    $variant =
                        ProductVariant::find(
                            (int) $variantId
                        );


                    /*
                     * Abaikan produk guest cart
                     * yang sudah tidak tersedia.
                     */
                    if (
                        !$variant
                        || $variant->status
                            !== 'active'
                        || $variant->stock < 1
                    ) {

                        continue;
                    }


                    $guestQuantity =
                        max(
                            1,
                            (int) $quantity
                        );


                    $cartItem =
                        CartItem::where(
                            'cart_id',
                            $cart->id
                        )
                            ->where(
                                'variant_id',
                                $variant->id
                            )
                            ->first();


                    /*
                     * Jika item sudah ada di
                     * cart akun, quantity
                     * digabung.
                     */
                    if ($cartItem) {

                        $newQuantity = min(
                            $cartItem->quantity
                                + $guestQuantity,
                            $variant->stock
                        );


                        $cartItem->update([

                            'quantity' =>
                                $newQuantity,

                            'price' =>
                                $variant->price,

                        ]);
                    } else {

                        $newQuantity = min(
                            $guestQuantity,
                            $variant->stock
                        );


                        CartItem::create([

                            'cart_id' =>
                                $cart->id,

                            'variant_id' =>
                                $variant->id,

                            'quantity' =>
                                $newQuantity,

                            'price' =>
                                $variant->price,

                        ]);
                    }
                }
            }
        );


        /*
         * Setelah berhasil dipindahkan
         * ke database, hapus guest cart.
         */
        $request
            ->session()
            ->forget(
                'guest_cart'
            );
    }


    private function redirectByRole(
        User $user
    ) {
        return match (
            $user->role?->name
        ) {

            'admin' =>
                redirect()
                    ->route(
                        'admin.dashboard'
                    ),

            'staff_gudang' =>
                redirect()
                    ->route(
                        'staff.dashboard'
                    ),

            'owner' =>
                redirect()
                    ->route(
                        'owner.dashboard'
                    ),

            /*
             * intended() penting.
             *
             * Guest klik checkout
             * -> diarahkan login
             * -> login berhasil
             * -> kembali ke checkout.
             */
            'customer' =>
                redirect()->intended(
                    route(
                        'customer.account'
                    )
                ),

            default =>
                abort(
                    403,
                    'Role akun tidak dikenali.'
                ),
        };
    }
}