    <!-- Header removed -->

    <form method="post" action="{{ route('profile.signature') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="signature" :value="__('Signature Image')" />
            
            @if($user->signature_path)
                <div class="mt-2 mb-4">
                    <img src="{{ asset('storage/' . $user->signature_path) }}" alt="Current Signature" class="h-24 border border-gray-200 rounded p-1">
                </div>
            @endif

            <input id="signature" name="signature" type="file" accept="image/png, image/jpeg" class="mt-1 block w-full text-sm text-primary-600/70
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100
            " />
            <x-input-error class="mt-2" :messages="$errors->get('signature')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'signature-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
