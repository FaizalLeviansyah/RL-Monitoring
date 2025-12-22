<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700">

        <div class="mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Create New User</h2>
            <p class="text-sm text-gray-500">Fill in the details to register a new employee.</p>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Full Name</label>
                    <input type="text" name="full_name" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition" required placeholder="e.g. John Doe">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Work Email</label>
                    <input type="email" name="email_work" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition" required placeholder="john@company.com">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">WhatsApp Number</label>
                    <input type="text" name="phone" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition" required placeholder="0812...">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Password</label>
                    <input type="password" name="password" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition" required>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Company</label>
                    <select name="company_id" id="company_select" onchange="loadDependencies(this.value)" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition" required>
                        <option value="">-- Select Company --</option>
                        @foreach($companies as $comp)
                            <option value="{{ $comp->company_id }}">{{ $comp->company_name }} ({{ $comp->company_code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Department</label>
                    <select name="department_id" id="dept_select" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition bg-gray-50" required disabled>
                        <option value="">-- Select Company First --</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">Position / Job Title</label>
                    <select name="position_id" id="pos_select" class="w-full rounded-xl border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition bg-gray-50" required disabled>
                        <option value="">-- Select Company First --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Jabatan akan muncul sesuai perusahaan yang dipilih.</p>
                </div>

            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 mr-3 text-sm font-medium text-gray-600 hover:text-gray-800 transition">Cancel</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                    Save User
                </button>
            </div>
        </form>
    </div>

    <script>
        async function loadDependencies(companyId) {
            const deptSelect = document.getElementById('dept_select');
            const posSelect = document.getElementById('pos_select');

            // Reset
            deptSelect.innerHTML = '<option value="">Loading...</option>';
            posSelect.innerHTML = '<option value="">Loading...</option>';
            deptSelect.disabled = true;
            posSelect.disabled = true;

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
                    deptHtml += `<option value="${d.department_id}">${d.department_name}</option>`;
                });
                deptSelect.innerHTML = deptHtml;
                deptSelect.disabled = false;

                // 2. Fetch Positions (Target Utama)
                const posRes = await fetch(`/api/get-positions/${companyId}`);
                const positions = await posRes.json();

                let posHtml = '<option value="">-- Select Position --</option>';
                positions.forEach(p => {
                    posHtml += `<option value="${p.position_id}">${p.position_name}</option>`;
                });
                posSelect.innerHTML = posHtml;
                posSelect.disabled = false;

            } catch (error) {
                console.error('Error fetching data:', error);
                alert('Gagal mengambil data departemen/posisi. Pastikan API route tersedia.');
                deptSelect.innerHTML = '<option value="">Error Loading Data</option>';
                posSelect.innerHTML = '<option value="">Error Loading Data</option>';
            }
        }
    </script>
</x-app-layout>
