<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Categories;

class CategoriesController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth:api');
    }

    public function index()
    {
        return Categories::withCount('medicines')->latest()->where('status_id', '!=', '7')->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:190',
            'remark' => 'nullable',
            'unit' => 'required',
            'status_id' => 'required'
        ]);

        return Categories::create([
            'name' => $request['name'],
            'remark' => $request['remark'],
            'unit' => strtoupper($request['unit']),
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        return Categories::where('id', $id)->get();
    }

    public function update(Request $request, $id)
    {
        $user = Categories::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:190',
            'remark' => 'nullable',
            'unit' => 'required',
            'status_id' => 'required'
        ]);
        $user->update([
                'name' => $request['name'],
                'remark' => $request['remark'],
                'unit' => strtoupper($request['unit']),
                'status_id' => $request['status_id']
        ]);
        return ['message' => 'updatng data'];
    }

    public function destroy($id)
    {
        $category = Categories::findOrFail($id);
        $category->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function search()
    {
        if($search = \Request::get('q')) {
            $categories = Categories::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%");
            })->withCount('medicines')->paginate(10);
        }
        else{
           $categories = Categories::withCount('medicines')->latest()->paginate(15);
        }
        return $categories;
    }

    public function getList()
    {
        return Categories::where('status_id', 2)->get();
    }
}
