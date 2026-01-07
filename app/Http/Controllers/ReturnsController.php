<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReturnsController extends Controller
{
    public function index()
    {
        return view('returns.index');
    }

    public function check(Request $request)
    {
        $request->validate(['query' => 'required|string']);
        $q = $request->input('query');
        $equipment = Equipment::where('code', $q)->orWhere('name', 'like', "%$q%")->first();
        if (!$equipment) {
            return back()->withErrors(['query' => 'Alat tidak ditemukan'])->withInput();
        }
        $loan = Loan::where('equipment_id', $equipment->id)->whereNull('returned_at')->first();
        if (!$loan) {
            return back()->with('info', 'Alat ini tidak memiliki peminjaman aktif.')->with(['equipment' => $equipment]);
        }
        $daysLate = max(now()->startOfDay()->diffInDays(Carbon::parse($loan->planned_return_at), false) * -1, 0);
        $fine = $daysLate * 1000;
        return view('returns.index', compact('equipment', 'loan', 'daysLate', 'fine'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|integer',
            'condition' => 'required|in:good,minor_damage,total_damage',
            'additional_fine' => 'nullable|integer',
        ]);

        $loan = Loan::where('id', $request->loan_id)->whereNull('returned_at')->firstOrFail();
        $daysLate = max(now()->startOfDay()->diffInDays(Carbon::parse($loan->planned_return_at), false) * -1, 0);
        $baseFine = $daysLate * 1000;
        $addFine = (int)($request->additional_fine ?? 0);
        $totalFine = $baseFine + $addFine;

        $loan->update([
            'returned_at' => now()->toDateString(),
            'status' => 'returned',
            'fine_amount' => $totalFine,
        ]);

        $equipment = Equipment::find($loan->equipment_id);
        if ($request->condition === 'good') {
            $equipment->update(['status' => 'available']);
        } else {
            $equipment->update(['status' => 'damaged']);
        }

        return redirect()->route('returns')->with('return_success', [
            'daysLate' => $daysLate,
            'totalFine' => $totalFine,
        ]);
    }
}