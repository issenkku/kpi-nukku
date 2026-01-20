<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Affiliation;

class AffiliationsController extends Controller
{
    public function index()
    {
        $affiliations = Affiliation::orderBy('name')->get();

        return view('Affiliation.app', compact('affiliations'));
    }

    public function store(Request $request)
    {
        $request->validate(([
            'name' => 'required|string|max:255',
        ]));

        $existingAffiliation = Affiliation::where('name', $request->name)->first();
        if ($existingAffiliation) {
            return redirect()->route('affiliations.index')->with('error', 'สังกัดงานนี้มีอยู่แล้ว');
        }

        Affiliation::create([
            'name' => $request->name,
        ]);

        return redirect()->route('affiliations.index')->with('success', 'สร้างสังกัดงานเรียบร้อย');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $affiliation = Affiliation::findOrFail($id);
        $existingAffiliation = Affiliation::where('name', $request->name)
            ->where('id', '!=', $affiliation->id)
            ->first();
        if ($existingAffiliation) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'ชื่อสังกัดงานนี้มีอยู่ในระบบ กรุณาใช้ชื่ออื่น'])
                ->with('edit_affiliation_id', $id);
        }

        $oldName = $affiliation->name;
        $affiliation->update([
            'name' => $request->name,
        ]);

        if ($oldName !== $request->name) {
            Department::where('work_group', $oldName)->update(['work_group' => $request->name]);
        }

        return redirect()->route('affiliations.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $affiliation = Affiliation::findOrFail($id);

        if (Department::where('work_group', $affiliation->name)->exists()) {
            return redirect()->route('affiliations.index')
                ->with('error', 'ไม่สามารถลบสังกัดงานนี้ได้ เพราะมีการใช้งานอยู่ในระบบ');
        }

        $affiliation->delete();
        return redirect()->route('affiliations.index')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }
}
