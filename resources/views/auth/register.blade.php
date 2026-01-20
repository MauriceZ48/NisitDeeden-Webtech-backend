<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="university_id" :value="__('University ID')" />
            <x-text-input id="university_id" class="block mt-1 w-full" type="text" name="university_id" :value="old('university_id')" required />
            <x-input-error :messages="$errors->get('university_id')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <x-input-label for="faculty" :value="__('Faculty')" />
                <select id="faculty" name="faculty" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Select faculty</option>
                    @foreach($faculties as $f)
                        <option value="{{ $f->value }}" {{ old('faculty') === $f->value ? 'selected' : '' }}>
                            {{ $f->value }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('faculty')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="department" :value="__('Department')" />
                <select id="department" name="department" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" disabled>
                    <option value="">Select faculty first</option>
                </select>
                <x-input-error :messages="$errors->get('department')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const facultySelect = document.getElementById('faculty');
            const deptSelect = document.getElementById('department');

            async function loadDepartments(selectedFaculty, selectedDept = '') {
                if (!selectedFaculty) {
                    deptSelect.innerHTML = '<option value="">Select faculty first</option>';
                    deptSelect.disabled = true;
                    return;
                }

                const res = await fetch(`/api/departments?faculty=${encodeURIComponent(selectedFaculty)}`);
                const data = await res.json();

                deptSelect.innerHTML = '<option value="">Select department</option>';
                data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.value;
                    opt.textContent = d.label;
                    if (selectedDept === d.value) opt.selected = true;
                    deptSelect.appendChild(opt);
                });
                deptSelect.disabled = false;
            }

            facultySelect.addEventListener('change', () => loadDepartments(facultySelect.value));

            if (facultySelect.value) {
                loadDepartments(facultySelect.value, @json(old('department')));
            }
        });
    </script>
</x-guest-layout>
