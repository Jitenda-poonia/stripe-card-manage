<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use App\Models\Card;

class CardController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }
    public function index()
    {
        $cards = Card::where('user_id', Auth::id())->get();
        return view('dashboard', compact('cards'));
    }


    public function store(Request $request)
    {
        $request->validate(['stripeToken' => 'required|string']);
        $user = Auth::user();

        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);
        } else {
            $customer = Customer::retrieve($user->stripe_customer_id);
        }

        $paymentMethod = PaymentMethod::create([
            'type' => 'card',
            'card' => ['token' => $request->stripeToken],
        ]);

        $paymentMethod->attach(['customer' => $customer->id]);

        Card::create([
            'user_id' => $user->id,
            'stripe_card_id' => $paymentMethod->id,
            'last4' => $paymentMethod->card->last4,
            'brand' => $paymentMethod->card->brand,
            'default' => $user->cards()->count() === 0,
        ]);

        return redirect()->route('dashboard');
    }

    public function setDefault(Card $card)
    {
        $user = Auth::user();

        if ($card->user_id != $user->id) {
            abort(403);
        }

        Card::where('user_id', $user->id)->update(['is_default' => false]);
        $card->is_default = true;
        $card->save();

        return redirect()->route('dashboard')->with('success', 'Default card updated.');
    }

    public function destroy(Card $card)
    {
        $user = Auth::user();

        if ($card->user_id != $user->id) {
            abort(403);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $card->delete();

        return redirect()->route('dashboard')->with('success', 'Card deleted successfully.');
    }
}

