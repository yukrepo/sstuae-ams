<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Doses;

class DosesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Doses::orderBy('code', 'asc')->paginate(15);
    }

    public function getList()
    {
        return Doses::get();
    }

    public function getSelectList()
    {
        $doses = Doses::get();;
        $result = [];
        foreach($doses as $dose):
            $result[] = ['value' => $dose->text, 'text' => $dose->code .' - '.$dose->text];
        endforeach;
        return $result;
    }
}
