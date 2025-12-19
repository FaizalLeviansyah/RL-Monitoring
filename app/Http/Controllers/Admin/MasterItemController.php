<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterItem;
use Illuminate\Http\Request;

class MasterItemController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterItem::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('item_name', 'LIKE', "%{$search}%")
                  ->orWhere('item_code', 'LIKE', "%{$search}%");
        }

        $items = $query->orderBy('item_name', 'asc')->paginate(10);
        return view('admin.master_items.index', compact('items'));
    }

    public function create()
    {
        return view('admin.master_items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_code' => 'required|unique:master_items,item_code',
            'item_name' => 'required|string|max:255',
            'unit'      => 'required|string',
        ]);

        MasterItem::create($request->all());

        return redirect()->route('admin.master-items.index')
                         ->with('success', 'Barang baru berhasil ditambahkan.');
    }

    public function edit(MasterItem $masterItem)
    {
        return view('admin.master_items.edit', compact('masterItem'));
    }

    public function update(Request $request, MasterItem $masterItem)
    {
        $request->validate([
            'item_code' => 'required|unique:master_items,item_code,' . $masterItem->id,
            'item_name' => 'required|string|max:255',
            'unit'      => 'required|string',
        ]);

        $masterItem->update($request->all());

        return redirect()->route('admin.master-items.index')
                         ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(MasterItem $masterItem)
    {
        $masterItem->delete();
        return back()->with('success', 'Barang berhasil dihapus.');
    }
}
