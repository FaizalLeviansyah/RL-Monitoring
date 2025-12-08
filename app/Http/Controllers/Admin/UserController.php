<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Fitur Filter Canggih
        $query = User::with(['company', 'department', 'position']);

        if($request->company_id) $query->where('company_id', $request->company_id);
        if($request->search) $query->where('full_name', 'like', '%'.$request->search.'%');

        $users = $query->paginate(10);
        $companies = Company::all(); // Untuk dropdown filter

        return view('admin.users.index', compact('users', 'companies'));
    }

    // Function Create/Store/Edit/Update/Delete standar CRUD
    // Tapi ingat koneksi ke 'mysql_master'
    // ... (Saya persingkat, konsepnya standar CRUD Laravel)
}