<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comments;

class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Comments::latest()->where('status_id', '!=', 7)->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
            'status_id' => 'required|numeric|max:10'
        ]);
        return Comments::create([
            'comment' => ucwords($request['comment']),
            'status_id' => $request['status_id']
        ]);
    }

    public function getList()
    {
        return Comments::where(['status_id' => 2])->get();
    }

    public function show($id)
    {
        $record = Comments::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Comments::findOrFail($id);
        $this->validate($request, [
            'comment' => 'required',
            'status_id' => 'nullable|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Comments::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Comments::where(function($query) use ($search){
                $query->where('comment','LIKE',"%$search%");
            })->latest()->paginate(15);
        }
        else{
           $records = '';
        }
        return $records;
    }

    public function search()
    {
        if($search = \Request::get('q')) {
            $records = Comments::where(function($query) use ($search){
                $query->where('comment','LIKE',"%$search%");
            })->get();
        }
        else{
           $records = [];
        }
        $result = [];
        foreach($records as $record):
            $result[$record->id] = $record->comment;
        endforeach;
        return $result;
    }
}
