<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\CartService;
use App\Services\CheckoutService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class Checkout extends Component
{
    // Customer Contact
    public string $contactName = '';

    public string $phone = '';

    public string $email = '';

    // Delivery Address Fields
    public ?int $selectedAddressId = null;

    public string $governorate = 'عمّان';

    public string $city = '';

    public string $district = '';

    public string $streetAddress = '';

    public string $buildingNumber = '';

    public string $floor = '';

    public string $apartment = '';

    public string $landmark = '';

    public string $deliveryInstructions = '';

    // Payment & Notes
    public string $paymentMethod = 'cash_on_delivery'; // 'cash_on_delivery' or 'cliq'

    public string $cliqAlias = '';

    public string $customerNotes = '';

    public bool $saveAddress = true;

    public bool $isSubmitting = false;

    public string $errorMessage = '';

    public function mount(CartService $cartService): void
    {
        $cart = $cartService->getActiveCart();
        if ($cart->items->isEmpty()) {
            $this->redirect(route('cart.index'));

            return;
        }

        if (Auth::check()) {
            $user = Auth::user();
            $this->contactName = $user->name;
            $this->phone = $user->phone ?? '';
            $this->email = $user->email;

            // Load default address if available
            $defaultAddress = $user->addresses()->where('is_default', true)->first()
                ?? $user->addresses()->latest()->first();

            if ($defaultAddress) {
                $this->selectSavedAddress($defaultAddress->id);
            }
        }
    }

    public function selectSavedAddress(int $addressId): void
    {
        $this->selectedAddressId = $addressId;
        $address = Address::where('user_id', Auth::id())->find($addressId);

        if ($address) {
            $this->contactName = $address->contact_name ?: $this->contactName;
            $this->phone = $address->phone ?: $this->phone;
            $this->governorate = $address->governorate;
            $this->city = $address->city;
            $this->district = $address->district ?? '';
            $this->streetAddress = $address->street_address ?? '';
            $this->buildingNumber = $address->building_number ?? '';
            $this->floor = $address->floor ?? '';
            $this->apartment = $address->apartment ?? '';
            $this->landmark = $address->landmark ?? '';
            $this->deliveryInstructions = $address->delivery_instructions ?? '';
        }
    }

    public function useNewAddress(): void
    {
        $this->selectedAddressId = null;
        $this->city = '';
        $this->district = '';
        $this->streetAddress = '';
        $this->buildingNumber = '';
        $this->floor = '';
        $this->apartment = '';
        $this->landmark = '';
        $this->deliveryInstructions = '';
    }

    public function updatedGovernorate(): void
    {
        // Re-render calculates new shipping based on governorate
    }

    public function placeOrder(CheckoutService $checkoutService, CartService $cartService)
    {
        $this->errorMessage = '';

        if ($this->isSubmitting) {
            return;
        }

        $this->isSubmitting = true;

        $rules = [
            'contactName' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'governorate' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'streetAddress' => ['required', 'string', 'max:255'],
            'paymentMethod' => ['required', 'in:cash_on_delivery,cliq'],
        ];

        if (! Auth::check()) {
            $rules['email'] = ['required', 'email', 'max:255'];
        }

        if ($this->paymentMethod === 'cliq') {
            $rules['cliqAlias'] = ['nullable', 'string', 'max:100'];
        }

        $this->validate($rules);

        try {
            // Resolve User (authenticated or auto-register guest)
            if (Auth::check()) {
                $user = Auth::user();
            } else {
                $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);
                if (str_starts_with($cleanPhone, '962')) {
                    $cleanPhone = '0'.substr($cleanPhone, 3);
                }

                $existingUser = User::where('email', strtolower($this->email))
                    ->orWhere('phone', $cleanPhone)
                    ->first();

                if ($existingUser) {
                    $user = $existingUser;
                } else {
                    $user = User::create([
                        'name' => $this->contactName,
                        'email' => strtolower($this->email),
                        'phone' => $cleanPhone,
                        'password' => Hash::make(Str::random(16)),
                        'role' => 'customer',
                        'status' => 'active',
                        'locale' => app()->getLocale(),
                    ]);

                    UserProfile::create(['user_id' => $user->id]);
                }

                Auth::login($user);
            }

            // Save address to user address book if requested
            if ($this->saveAddress && ! $this->selectedAddressId) {
                Address::create([
                    'user_id' => $user->id,
                    'type' => 'shipping',
                    'contact_name' => $this->contactName,
                    'phone' => $this->phone,
                    'governorate' => $this->governorate,
                    'city' => $this->city,
                    'district' => $this->district ?: $this->city,
                    'street_address' => $this->streetAddress,
                    'building_number' => $this->buildingNumber,
                    'floor' => $this->floor,
                    'apartment' => $this->apartment,
                    'landmark' => $this->landmark,
                    'delivery_instructions' => $this->deliveryInstructions,
                    'is_default' => $user->addresses()->count() === 0,
                ]);
            }

            $order = $checkoutService->processCheckout($user, [
                'contact_name' => $this->contactName,
                'phone' => $this->phone,
                'email' => $user->email,
                'governorate' => $this->governorate,
                'city' => $this->city,
                'district' => $this->district,
                'street_address' => $this->streetAddress,
                'building_number' => $this->buildingNumber,
                'floor' => $this->floor,
                'apartment' => $this->apartment,
                'landmark' => $this->landmark,
                'delivery_instructions' => $this->deliveryInstructions,
                'customer_notes' => $this->customerNotes,
                'payment_method' => $this->paymentMethod,
                'cliq_alias' => $this->cliqAlias,
            ]);

            $this->dispatch('cart-updated');

            return redirect()->route('orders.confirmation', $order->order_number);

        } catch (Exception $e) {
            $this->isSubmitting = false;
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render(CartService $cartService)
    {
        $cart = $cartService->getActiveCart();
        $cart->load(['items.offer.product', 'items.offer.seller']);

        $totals = $cartService->getTotals($this->governorate);

        $savedAddresses = Auth::check()
            ? Auth::user()->addresses()->latest()->get()
            : collect();

        $governorates = [
            'عمّان' => 'عمّان (العاصمة)',
            'الزرقاء' => 'الزرقاء',
            'إربد' => 'إربد',
            'العقبة' => 'العقبة',
            'البلقاء' => 'البلقاء (السلط)',
            'مأدبا' => 'مأدبا',
            'المفرق' => 'المفرق',
            'جرش' => 'جرش',
            'عجلون' => 'عجلون',
            'الكرك' => 'الكرك',
            'الطفيلة' => 'الطفيلة',
            'معان' => 'معان',
        ];

        return view('livewire.checkout', [
            'cart' => $cart,
            'totals' => $totals,
            'savedAddresses' => $savedAddresses,
            'governorates' => $governorates,
            'appliedCoupon' => $totals['coupon'],
        ]);
    }
}
