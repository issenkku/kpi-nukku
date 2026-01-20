<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Affiliation;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('id', 'asc')->get();
        $affiliations = Affiliation::query()
            ->orderBy('name')
            ->pluck('name');
        return view('departments.app', compact('departments', 'affiliations'));
    }
    public function store(Request $request)
    {
        $request->validate(([
            'name' => 'required|string|max:255',
            'work_group' => 'nullable|string|max:255',
        ]));

        //ตรวจสอบว่ามีหน่วยงานนี้อยู่แล้วหรือไม่
        $existingDepartment = Department::where('name', $request->name)->first();
        if ($existingDepartment) {
            return redirect()->route('departments.index')->with('error', 'หน่วยงานนี้มีอยู่แล้ว');
        }
        Department::create([
            'name' => $request->name,
            'work_group' => $request->work_group,
        ]);
        // return response()->json([
        //     'message' => 'หน่วยงานถูกสร้างเรียบร้อยแล้ว',
        //     'data' => $request->name
        // ], 201);
        return redirect()->route('departments.index')->with('success', 'สร้างหน่วยงานเรียบร้อย');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'work_group' => 'nullable|string|max:255',
        ]);
        //ตรวจสอบชิ่อหน่วยงานซ้ำ (ยกเว้นตัวเอง)
        $existingDepartment = Department::where('name', $request->name)
            ->where('id', '!=', $id)
            ->first();
        if ($existingDepartment) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'ชื่อหน่วยงานนี้มีอยู่ในระบบ กรุณาใช้ชื่ออื่น'])
                ->with('edit_department_id', $id);
        }
        $departments = Department::findOrFail($id);
        $departments->update([
            'name' => $request->name,
            'work_group' => $request->work_group,
        ]);
        return redirect()->route('departments.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }
    public function destroy($id)
    {
        $department = Department::findOrFail($id);

        //ตรวจสอบว่ามีผู้ใช้งานในหน่วยงานนี้หรือไม่
        if ($department->users()->count() > 0) {
            return redirect()->route('departments.index')
                ->with('error', 'ไม่สามารถลบหน่วยงานนี้ได้ เพราะมีการใช้งานอยู่ในระบบ');
        }
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }
}
