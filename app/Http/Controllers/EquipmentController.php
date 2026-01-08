<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') { abort(403); }
        $q = $request->query('q');
        $equipments = Equipment::when($q, function ($query) use ($q) {
            $query->where('name', 'like', "%$q%")->orWhere('code', 'like', "%$q%");
        })->orderBy('id', 'desc')->get();
        return view('equipment.index', compact('equipments', 'q'));
    }

    public function printAll()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') { abort(403); }
        $equipments = Equipment::orderBy('name')->get();
        return view('equipment.qr_print', compact('equipments'));
    }

    public function printSingle($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') { abort(403); }
        $eq = Equipment::findOrFail($id);
        return view('equipment.qr_single', compact('eq'));
    }

    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') { abort(403); }
        $data = $request->validate([
            'name' => 'required|string',
            'code' => 'nullable|string|unique:equipment,code',
            'status' => 'required|in:available,loaned,damaged',
            'image' => 'nullable|image|max:2048',
        ]);
        $eq = Equipment::create([
            'name' => $data['name'],
            'status' => $data['status'],
            'code' => $data['code'] ?? null,
        ]);
        if (!$eq->code) {
            $eq->code = 'TL-'.str_pad($eq->id, 3, '0', STR_PAD_LEFT);
            $eq->save();
        }
        if ($request->hasFile('image')) {
            $dir = public_path('img/equipment');
            if (!\Illuminate\Support\Facades\File::exists($dir)) { \Illuminate\Support\Facades\File::makeDirectory($dir, 0755, true); }
            $filename = 'eq-'.$eq->id.'.png';
            $request->file('image')->move($dir, $filename);
            $eq->image_path = 'img/equipment/'.$filename;
            $eq->save();
        }
        return redirect()->route('equipment')->with('msg', 'Alat ditambahkan');
    }

    public function update($id, Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') { abort(403); }
        $eq = Equipment::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string',
            'code' => 'nullable|string|unique:equipment,code,'.$eq->id,
            'status' => 'required|in:available,loaned,damaged',
            'image' => 'nullable|image|max:2048',
        ]);
        $eq->update($data);
        if ($request->hasFile('image')) {
            $dir = public_path('img/equipment');
            if (!\Illuminate\Support\Facades\File::exists($dir)) { \Illuminate\Support\Facades\File::makeDirectory($dir, 0755, true); }
            $filename = 'eq-'.$eq->id.'.png';
            $request->file('image')->move($dir, $filename);
            $eq->image_path = 'img/equipment/'.$filename;
            $eq->save();
        }
        return redirect()->route('equipment')->with('msg', 'Alat diperbarui');
    }

    public function destroy($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') { abort(403); }
        $eq = Equipment::findOrFail($id);
        if ($eq->image_path && file_exists(public_path($eq->image_path))) {
            @unlink(public_path($eq->image_path));
        }
        $eq->delete();
        return redirect()->route('equipment')->with('msg', 'Alat dihapus');
    }
}