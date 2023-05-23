<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Instructions;

class InstructionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Instructions::orderBy('code', 'asc')->paginate(15);
    }

    public function getList()
    {
        return Instructions::get();
    }

    public function getSelectList()
    {
        $instructions = Instructions::get();;
        $result = [];
        foreach($instructions as $instruction):
            $result[] = ['code' => $instruction->code, 'text' => $instruction->code .' - '.$instruction->text, 'value' => $instruction->text];
        endforeach;
        return $result;
    }
}
