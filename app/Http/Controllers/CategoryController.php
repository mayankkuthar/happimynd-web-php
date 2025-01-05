<?php

namespace App\Http\Controllers;

use App\Models\BatchCategory;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getAllCategories(Request $request)
    {
        $categories  = BatchCategory::with('batch:id,name', 'category')->whereHas('category.batch')->whereHas('batch')->withCount('questions')->get();
        return view('Backend.assessment.category')->with('categories', $categories);
    }
    public function addCategory(Request $request)
    {
        Category::create([
            'name' => $request->input('categoryName'),
            'acronymn' => $request->input('categoryAcronymn'),
            'name_in_report' => $request->input('categoryNameInReport'),
            'color' => $request->input('color')
        ]);
        return redirect(route('admin.getAllCategories.get'));
    }

    public function deleteCategory(Request $request)
    {
        return Category::find($request->input('category_id'))->delete();
    }

    public function updateCategory(Request $request)
    {
        return Category::where('id', $request->input('categoryId'))->update([
            'name' => $request->input('categoryName'),
            'acronymn' => $request->input('categoryAcronymn'),
            "name_in_report" => $request->input('categoryNameInReport'),
            'color' => $request->input('color')
        ]);
    }
}
