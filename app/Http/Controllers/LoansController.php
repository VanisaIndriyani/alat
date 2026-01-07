<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Staff;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoansController extends Controller
{
    public function index(Request $request)
    {
        $cart = session('loan_cart', []);
        $staff = Staff::orderBy('name')->get();
        return view('loans.index', [
            'cart' => $cart,
            'results' => [],
            'query' => '',
            'staff' => $staff,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate(['query' => 'required|string']);
        $q = $request->input('query');
        $results = Equipment::where('status', 'available')
            ->where(function ($w) use ($q) {
                $w->where('code', $q)->orWhere('name', 'like', "%$q%");
            })->limit(10)->get();
        $cart = session('loan_cart', []);
        $staff = Staff::orderBy('name')->get();
        return view('loans.index', [
            'cart' => $cart,
            'results' => $results,
            'query' => $q,
            'staff' => $staff,
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate(['equipment_id' => 'required|integer']);
        $eq = Equipment::where('id', $request->equipment_id)->where('status', 'available')->firstOrFail();
        $cart = session('loan_cart', []);
        if (!collect($cart)->contains(fn ($i) => $i['id'] === $eq->id)) {
            $cart[] = ['id' => $eq->id, 'name' => $eq->name, 'code' => $eq->code];
            session(['loan_cart' => $cart]);
        }
        return redirect()->route('loans')->with('cart_msg', 'Alat ditambahkan ke keranjang');
    }

    public function removeFromCart(Request $request)
    {
        $request->validate(['equipment_id' => 'required|integer']);
        $cart = collect(session('loan_cart', []))->reject(fn ($i) => $i['id'] == $request->equipment_id)->values()->all();
        session(['loan_cart' => $cart]);
        return redirect()->route('loans')->with('cart_msg', 'Alat dihapus dari keranjang');
    }

    public function process(Request $request)
    {
        $request->validate([
            'borrower_nis' => 'required|string',
            'borrower_name' => 'required|string',
            'planned_return' => 'required|date',
            'purpose' => 'required|string',
        ]);

        $cart = session('loan_cart', []);
        if (empty($cart)) {
            return redirect()->route('loans')->withErrors(['cart' => 'Keranjang kosong']);
        }

        foreach ($cart as $item) {
            $eq = Equipment::findOrFail($item['id']);
            if ($eq->status !== 'available') { continue; }
            Loan::create([
                'student_name' => $request->borrower_name,
                'student_nis' => $request->borrower_nis,
                'equipment_id' => $eq->id,
                'borrowed_at' => now()->toDateString(),
                'planned_return_at' => $request->planned_return,
                'purpose' => $request->purpose,
                'status' => 'active',
                'returned_at' => null,
            ]);
            $eq->update(['status' => 'loaned']);
        }

        session()->forget('loan_cart');
        return redirect()->route('loans')->with('loan_success', 'Peminjaman diproses: '.count($cart).' item.');
    }
}