<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informasi Profil') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Informasi dasar akun Anda (Dikelola oleh IT Department).
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="photo" :value="__('Ganti Foto Profil')" />
            <input type="file" name="photo" id="photo" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 mt-1">
            <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF (Max 2MB).</p>
        </div>

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-100 cursor-not-allowed" :value="old('name', $user->full_name)" disabled />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email Kerja')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-100 cursor-not-allowed" :value="old('email', $user->email_work)" disabled />
        </div>
        
        <div>
            <x-input-label for="dept" :value="__('Departemen')" />
            <x-text-input id="dept" type="text" class="mt-1 block w-full bg-gray-100 cursor-not-allowed" :value="$user->department->department_name ?? '-'" disabled />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan Foto') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>