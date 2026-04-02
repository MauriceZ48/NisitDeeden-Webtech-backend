<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-slate-900">เข้าสู่ระบบ</h2>
        <p class="mt-2 text-sm text-slate-500">ระบบพิจารณารางวัลนิสิตดีเด่น</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="email" :value="__('อีเมล')" class="font-semibold text-slate-700" />

            <x-text-input id="email"
                          class="block mt-1.5 w-full rounded-xl border-slate-200 px-4 py-3 text-sm focus:border-primary focus:ring-primary/20 transition-colors shadow-sm"
                          type="email"
                          name="email"
                          :value="old('email')"
                          required
                          autofocus
                          autocomplete="username"
                          placeholder="example@ku.th" />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('รหัสผ่าน')" class="font-semibold text-slate-700" />

            <x-text-input id="password"
                          class="block mt-1.5 w-full rounded-xl border-slate-200 px-4 py-3 text-sm focus:border-primary focus:ring-primary/20 transition-colors shadow-sm"
                          type="password"
                          name="password"
                          required
                          autocomplete="current-password"
                           />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="w-full flex justify-center items-center rounded-xl bg-primary px-4 py-3.5 text-sm font-bold text-white shadow-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all">
                เข้าสู่ระบบ
            </button>
        </div>
    </form>
</x-guest-layout>
