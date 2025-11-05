<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Standard;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        $standards = Standard::all();
        $categories = Category::OrderBy('id', 'asc')->get();
        return view('categories.app', compact('standards', 'categories'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:0',
            'standard_id' => 'required|exists:standards,id'
        ]);
        $existingcategories = Category::where('name', $request->name)->first();
        if ($existingcategories) {
            return redirect()->route('categories.index')->with('error', 'ด้านนี้มีอยู่แล้ว');
        }
        Category::create([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'standard_id' => $request->standard_id
        ]);
        return redirect()->route('categories.index')->with('success', 'สร้างด้านนี้เรียบร้อย');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:0',
            'standard_id' => 'required|exists:standards,id'
        ]);

        // ตรวจสอบซ้ำชื่อที่ไม่ใช่ตัวเอง
        // $existingCategory = Category::where('name', $request->name)
        //     ->where('id', '!=', $id)
        //     ->first();

        // if ($existingCategory) {
        //     return redirect()->back()
        //         ->withInput()
        //         ->withErrors(['name' => 'ชื่อด้านนี้มีอยู่ในระบบ กรุณาใช้ชื่ออื่น']);
        // }

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'standard_id' => $request->standard_id,
        ]);

        return redirect()->route('categories.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // ตรวจสอบว่ามี Indicator ที่เชื่อมโยงกับ Category นี้หรือไม่
        if ($category->indicators()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'ไม่สามารถลบด้านนี้ได้ เพราะมีการใช้งานอยู่ในระบบ (มีตัวบ่งชี้ที่ใช้งาน)');
        }

       
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }
}
