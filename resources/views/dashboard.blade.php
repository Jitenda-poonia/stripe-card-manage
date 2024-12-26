<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
        <div class="flex justify-end space-x-4">
            <form action="{{ route('cards.store') }}" method="POST">
                @csrf
                <script
                    src="https://checkout.stripe.com/checkout.js"
                    class="stripe-button"
                    data-key="{{ config('services.stripe.public') }}"
                    data-name="Add Card"
                    data-description="Save your card"
                    data-label="Add Card"
                    data-email="{{ auth()->user()->email }}"
                ></script>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">Your Cards</h1>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @isset($cards)
                            @foreach ($cards as $card)
                                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md p-4">
                                    <h2 class="font-semibold text-lg">{{ $card->brand }}</h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Ending in {{ $card->last4 }}
                                    </p>
                                    @if ($card->is_default)
                                        <span class="text-green-600 text-sm font-medium">(Default)</span>
                                    @endif
                                    <div class="mt-4 flex justify-between space-x-2">
                                        <form action="{{ route('cards.setDefault', $card) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:underline">
                                                Set as Default
                                            </button>
                                        </form>
                                        <form action="{{ route('cards.destroy', $card) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>No cards available.</p>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
