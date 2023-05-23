<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Options;

class OptionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Options::all();
    }

    public function update(Request $request, $id)
    {
        $record = Options::findOrFail($id);
        $this->validate($request, [
            'value' => 'required|string|max:250',
            'code' => 'required|max:25|unique:oe_categories,code,'.$record->id,
            'status_id' => 'nullable|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function getList()
    {
        $alloptions = Options::all();
        foreach($alloptions as $option){
            $options[$option->name] = $option->value;
        }
        return $options;
    }

}
