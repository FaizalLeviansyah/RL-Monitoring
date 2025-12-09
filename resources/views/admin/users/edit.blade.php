<x-app-layout>
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <nav class="flex mb-5" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                                Home
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.users.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">Users</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">Edit User</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Edit Employee: {{ $user->employee_code }}</h1>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6">
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">

            <form action="{{ route('admin.users.update', $user->employee_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="col-span-2 sm:col-span-1">
                        <label for="full_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Full Name</label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $user->full_name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label for="email_work" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Work Email</label>
                        <input type="email" name="email_work" id="email_work" value="{{ old('email_work', $user->email_work) }}" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">New Password</label>
                        <input type="text" name="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Biarkan kosong jika tidak ingin mengganti password.</p>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label for="employment_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                        <select name="employment_status" id="employment_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="Active" {{ $user->employment_status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $user->employment_status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Resigned" {{ $user->employment_status == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                        </select>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label for="company_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company (PT)</label>
                        <select name="company_id" id="company_id" onchange="loadMasterData(this.value)" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                            <option value="">Select Company</option>
                            @foreach($companies as $comp)
                                <option value="{{ $comp->company_id }}" {{ $user->company_id == $comp->company_id ? 'selected' : '' }}>
                                    {{ $comp->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label for="department_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Department</label>
                        <select name="department_id" id="department_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                            <option value="">Select Company First</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label for="position_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Position / Role</label>
                        <select name="position_id" id="position_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                            <option value="">Select Company First</option>
                        </select>
                        <p class="mt-1 text-xs text-blue-500">Note: 'Manager' & 'Director' will have approval rights.</p>
                    </div>
                </div>

                <div class="items-center pt-6 mt-6 border-t border-gray-200 rounded-b dark:border-gray-700 flex justify-end gap-3">
                    <a href="{{ route('admin.users.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Cancel</a>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Data lama user (dikirim dari controller)
        // Kita gunakan optional chaining (?.) untuk safety jika data null (walau seharusnya tidak)
        const oldDeptId = "{{ $user->department_id }}";
        const oldPosId = "{{ $user->position_id }}";
        const currentCompanyId = document.getElementById('company_id').value;

        // Fungsi Utama Load Data
        async function loadMasterData(companyId, isInit = false) {
            const deptSelect = document.getElementById('department_id');
            const posSelect = document.getElementById('position_id');

            // Reset options hanya menampilkan Loading...
            deptSelect.innerHTML = '<option value="">Loading...</option>';
            posSelect.innerHTML = '<option value="">Loading...</option>';

            if (!companyId) {
                deptSelect.innerHTML = '<option value="">Select Company First</option>';
                posSelect.innerHTML = '<option value="">Select Company First</option>';
                return;
            }

            try {
                // 1. Fetch Departments
                const resDept = await fetch(`/api/get-departments/${companyId}`);
                const dataDept = await resDept.json();

                deptSelect.innerHTML = '<option value="">Select Department</option>';
                dataDept.forEach(dept => {
                    const opt = document.createElement('option');
                    opt.value = dept.department_id;
                    opt.textContent = dept.department_name;
                    // Auto select jika ini inisialisasi awal dan ID cocok
                    if(isInit && dept.department_id == oldDeptId) opt.selected = true;
                    deptSelect.appendChild(opt);
                });

                // 2. Fetch Positions
                const resPos = await fetch(`/api/get-positions/${companyId}`);
                const dataPos = await resPos.json();

                posSelect.innerHTML = '<option value="">Select Position</option>';
                dataPos.forEach(pos => {
                    const opt = document.createElement('option');
                    opt.value = pos.position_id;
                    opt.textContent = pos.position_name;
                    // Auto select jika ini inisialisasi awal dan ID cocok
                    if(isInit && pos.position_id == oldPosId) opt.selected = true;
                    posSelect.appendChild(opt);
                });

            } catch (error) {
                console.error('Error:', error);
                deptSelect.innerHTML = '<option value="">Error loading data</option>';
                posSelect.innerHTML = '<option value="">Error loading data</option>';
            }
        }

        // Jalankan saat halaman pertama kali dibuka untuk mengisi data awal
        document.addEventListener('DOMContentLoaded', () => {
            if(currentCompanyId) {
                loadMasterData(currentCompanyId, true);
            }
        });
    </script>
</x-app-layout>
