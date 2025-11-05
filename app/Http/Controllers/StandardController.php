<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Standard;
use Illuminate\Http\Request;

class StandardController extends Controller
{
    public function index()
    {
        $standards = Standard::OrderBy('id', 'asc')->get();
        $categories = Category::all();
        return view('categories.app', compact('standards', 'categories'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $existingstandards = Standard::where('name', $request->name)->first();
        if ($existingstandards) {
            return redirect()->route('standards.index')->with('error', 'มาตรฐานนี้มีอยู่แล้ว');
        }
        Standard::create([
            'name' => $request->name,
        ]);
        return redirect()->route('standards.index')->with('success', 'สร้างมาตรฐานนี้เรียบร้อย');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // ตรวจสอบชื่อซ้ำ (ไม่รวมตัวเอง)
        $existingStandard = Standard::where('name', $request->name)
            ->where('id', '!=', $id)
            ->first();

        if ($existingStandard) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['name' => 'ชื่อมาตรฐานนี้มีอยู่ในระบบ กรุณาใช้ชื่ออื่น']);
        }

        $standard = Standard::findOrFail($id);
        $standard->update([
            'name' => $request->name,
        ]);

        return redirect()->route('standards.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $standard = Standard::findOrFail($id);

        // ตรวจสอบว่ามี Indicator ที่เชื่อมโยงกับ Category นี้หรือไม่
        if ($standard->indicators()->count() > 0) {
            return redirect()->route('standards.index')
                ->with('error', 'ไม่สามารถลบมาตรฐานนี้ได้ เพราะมีการใช้งานอยู่ในระบบ (มีตัวบ่งชี้ที่ใช้งาน)');
        }


        $standard->delete();

        return redirect()->route('standards.index')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }
}
