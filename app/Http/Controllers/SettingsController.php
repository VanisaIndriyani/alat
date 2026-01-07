<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        $setting = AppSetting::first();
        $staff = Staff::orderBy('name')->get();
        $students = Student::orderBy('name')->get();
        return view('settings.index', compact('setting','staff','students'));
    }

    public function saveInstitution(Request $request)
    {
        $data = $request->validate([
            'school_name' => 'nullable|string',
            'department_name' => 'nullable|string',
            'address' => 'nullable|string',
            'head_name' => 'nullable|string',
            'head_nip' => 'nullable|string',
            'theme_primary' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'logo' => 'nullable|image',
        ]);
        $setting = AppSetting::first() ?? new AppSetting();
        if ($request->hasFile('logo')) {
            $dir = public_path('img');
            if (!File::exists($dir)) { File::makeDirectory($dir, 0755, true); }
            $request->file('logo')->move($dir, 'logo.png');
            $data['logo_path'] = 'img/logo.png';
        }
        $setting->fill($data)->save();
        return redirect()->route('settings')->with('msg','Profil instansi disimpan');
    }

    public function staffStore(Request $request)
    {
        $data = $request->validate(['name'=>'required|string','position'=>'nullable|string']);
        Staff::create($data);
        return redirect()->route('settings')->with('msg','Petugas ditambahkan');
    }

    public function staffDelete($id)
    {
        Staff::findOrFail($id)->delete();
        return redirect()->route('settings')->with('msg','Petugas dihapus');
    }

    public function staffUpdate($id, Request $request)
    {
        $data = $request->validate(['name'=>'required|string','position'=>'nullable|string']);
        Staff::findOrFail($id)->update($data);
        return redirect()->route('settings')->with('msg','Petugas diperbarui');
    }

    public function studentStore(Request $request)
    {
        $data = $request->validate([
            'nis'=>'required|string|unique:students,nis',
            'name'=>'required|string',
            'class'=>'nullable|string',
            'type'=>'required|in:student,teacher'
        ]);
        Student::create($data);
        return redirect()->route('settings')->with('msg','Peminjam ditambahkan');
    }

    public function studentDelete($id)
    {
        Student::findOrFail($id)->delete();
        return redirect()->route('settings')->with('msg','Peminjam dihapus');
    }

    public function studentUpdate($id, Request $request)
    {
        $st = Student::findOrFail($id);
        $data = $request->validate([
            'nis'=>'required|string|unique:students,nis,'.$st->id,
            'name'=>'required|string',
            'class'=>'nullable|string',
            'type'=>'required|in:student,teacher'
        ]);
        $st->update($data);
        return redirect()->route('settings')->with('msg','Peminjam diperbarui');
    }

    public function studentFind(Request $request)
    {
        $nis = $request->query('nis');
        $s = Student::where('nis',$nis)->first();
        if(!$s) return response()->json(['found'=>false]);
        return response()->json(['found'=>true,'name'=>$s->name,'type'=>$s->type,'class'=>$s->class]);
    }
}