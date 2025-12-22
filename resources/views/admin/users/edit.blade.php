<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700">

        <div class="mb-6 border-b border-gray-100 dark:border-gray-700 pb-4 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Edit User Profile</h2>
                <p class="text-sm text-gray-500">Update information for <span class="font-bold text-blue-600">{{ $user->full_name }}</span>.</p>
            </div>
            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $user->employment_status == 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $user->employment_status }}
            </span>
        </div>

        <form action="{{ route('admin.users.update', $user->employee_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Full Name</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition" required>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Work Email</label>
                    <input type="email" name="email_work" value="{{ old('email_work', $user->email_work) }}" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition" required>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">WhatsApp Number</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone) }}" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition" required>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Company</label>
                    <select name="company_id" id="company_select" onchange="loadDependencies(this.value)" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition" required>
                        <option value="">-- Select Company --</option>
                        @foreach($companies as $comp)
                            <option value="{{ $comp->company_id }}" {{ $user->company_id == $comp->company_id ? 'selected' : '' }}>
                                {{ $comp->company_name }} ({{ $comp->company_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Employment Status</label>
                    <select name="employment_status" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="Active" {{ $user->employment_status == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Resigned" {{ $user->employment_status == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                        <option value="Inactive" {{ $user->employment_status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Department</label>
                    <select name="department_id" id="dept_select" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition bg-gray-50" required>
                        <option value="">Loading...</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Position</label>
                    <select name="position_id" id="pos_select" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition bg-gray-50" required>
                        <option value="">Loading...</option>
                    </select>
                </div>

                <div class="md:col-span-2 pt-4 border-t border-gray-100 dark:border-gray-700 mt-2">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Change Password (Optional)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block mb-2 text-xs font-bold text-gray-500">New Password</label>
                            <input type="password" name="password" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" placeholder="Leave blank to keep current">
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-bold text-gray-500">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" placeholder="Retype password">
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700 gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 transition">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                    Update User
                </button>
            </div>
        </form>
    </div>

    <script>
        // Data Awal User (Dipakai untuk auto-select saat halaman load)
        const initialData = {
            companyId: "{{ $user->company_id }}",
            deptId: "{{ $user->department_id }}",
            posId: "{{ $user->position_id }}"
        };

        // Fungsi Load Data (Bisa untuk Init atau Change)
        async function loadDependencies(companyId, selectedDept = null, selectedPos = null) {
            const deptSelect = document.getElementById('dept_select');
            const posSelect = document.getElementById('pos_select');

            // Set loading state
            if (!selectedDept) deptSelect.innerHTML = '<option value="">Loading...</option>';
            if (!selectedPos) posSelect.innerHTML = '<option value="">Loading...</option>';

            if (!companyId) {
                deptSelect.innerHTML = '<option value="">-- Select Company First --</option>';
                posSelect.innerHTML = '<option value="">-- Select Company First --</option>';
                return;
            }

            try {
                // 1. Fetch Departments
                const deptRes = await fetch(`/api/get-departments/${companyId}`);
                const depts = await deptRes.json();

                let deptHtml = '<option value="">-- Select Department --</option>';
                depts.forEach(d => {
                    const isSelected = (selectedDept && d.department_id == selectedDept) ? 'selected' : '';
                    deptHtml += `<option value="${d.department_id}" ${isSelected}>${d.department_name}</option>`;
                });
                deptSelect.innerHTML = deptHtml;
                deptSelect.disabled = false;

                // 2. Fetch Positions
                const posRes = await fetch(`/api/get-positions/${companyId}`);
                const positions = await posRes.json();

                let posHtml = '<option value="">-- Select Position --</option>';
                positions.forEach(p => {
                    const isSelected = (selectedPos && p.position_id == selectedPos) ? 'selected' : '';
                    posHtml += `<option value="${p.position_id}" ${isSelected}>${p.position_name}</option>`;
                });
                posSelect.innerHTML = posHtml;
                posSelect.disabled = false;

            } catch (error) {
                console.error('Error fetching data:', error);
                deptSelect.innerHTML = '<option value="">Error Loading Data</option>';
                posSelect.innerHTML = '<option value="">Error Loading Data</option>';
            }
        }

        // Panggil saat halaman pertama kali dimuat
        document.addEventListener('DOMContentLoaded', function() {
            if (initialData.companyId) {
                loadDependencies(initialData.companyId, initialData.deptId, initialData.posId);
            }
        });
    </script>
</x-app-layout>
