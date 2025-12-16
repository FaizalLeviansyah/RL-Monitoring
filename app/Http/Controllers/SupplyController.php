<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionItem;
use App\Models\SupplyHistory;
use App\Models\RequisitionLetter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:requisition_items,id',
            'qty_received' => 'required|integer|min:1',
            'photo_proof' => 'nullable|image|max:2048', // Maks 2MB
            'delivery_note' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $item = RequisitionItem::findOrFail($request->item_id);

            // 1. Cek Validasi: Jangan sampai terima lebih dari yang diminta
            $sisa = $item->qty - $item->supplyHistories->sum('qty_received');

            if ($request->qty_received > $sisa) {
                // Kalau input kelebihan, kita paksa jadi sisanya saja (biar gak error)
                $qtyToInput = $sisa;
            } else {
                $qtyToInput = $request->qty_received;
            }

            // 2. Simpan Bukti Foto (Jika ada)
            $photoPath = null;
            if ($request->hasFile('photo_proof')) {
                $photoPath = $request->file('photo_proof')->store('supply_proofs', 'public');
            }

            SupplyHistory::create([
                'rl_item_id' => $item->id,
                'received_by' => Auth::user()->employee_id,
                'qty_received' => $qtyToInput,
                'photo_proof' => $photoPath,
            ]);

            $totalReceived = $item->supplyHistories()->sum('qty_received'); // Query ulang biar akurat

            if ($totalReceived >= $item->qty) {
                $item->update(['status_item' => 'SUPPLIED']);
            }

            $rl = $item->letter;
            $allItemsSupplied = $rl->items()->where('status_item', '!=', 'SUPPLIED')->doesntExist();

            if ($allItemsSupplied) {
                $rl->update(['status_flow' => 'COMPLETED']); // Tamat!
            } else {
                // Jika status masih APPROVED tapi sudah ada barang masuk, bisa diubah jadi PARTIAL (Opsional)
                // $rl->update(['status_flow' => 'PARTIAL_SUPPLY']);
            }
        });

        return back()->with('success', 'Barang berhasil diterima!');
    }
}
