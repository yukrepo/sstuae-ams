<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Insurances;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DB;
use App\Models\Users;
use App\Models\UserProfiles;
use App\Models\UserDocuments;
use App\Models\Nationalities;

class ImportController extends Controller
{

    public function parseImport(Request $request)
    {
        $file = $request->file('csv_file');

        if ($file) 
        {
            ini_set('max_execution_time', 12000);
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();

            $location = 'temps'; 
        
            $file->move($location, $filename);
            $filepath = public_path($location . "/" . $filename);
            $file = fopen($filepath, "r");
            $importData_arr = array();
            $i = 0;
            
            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);
                if ($i == 0) 
                {
                    $i++;
                    continue;
                }

                for ($c = 0; $c < $num; $c++) 
                {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }

            fclose($file); 
            $j = 0;
        } 
        else {
            throw new \Exception('No file was uploaded', Response::HTTP_BAD_REQUEST);
        }
        echo '<pre>';
        //print_r($importData_arr); die;
        //$ids = Users::get()->pluck('ref_no')->toArray();
        foreach($importData_arr as $key => $data) {
            $name = explode(' ', $data[0], 2);
            DB::table('test')->insert([
                'first_name' => $name[0],
                'last_name' => (count($name) ==2)?$name[1]:'',
                'mobile' => '971'.$data[2],
                'dob' => $data[1],
            ]);
        }; 
        die;
    }

    public function adduser($data = null)
    {
        $number = 'S'.date('y').str_pad(Users::get()->count()+1, 4, "0",STR_PAD_LEFT);
        $user = Users::create([
                'mobile' => '971'.$data[5],
                'password' => Hash::make('971'.$data[5]),
                'username' => $number,
                'status_id' => 2,
                'admin_id' => 1,
                'source' => 'DUBAI CLINIC',
                'api_token' => Str::random(60),
                'ref_no' => $data[0]
        ]);
        $name = explode(' ', $data[1]);
        $c = DB::table('countries')->where('country', $data[4])->first();
        if($c):
            $n = (Nationalities::where('nationality', $c->nationalitysingular)->first())?Nationalities::where('nationality', $c->nationalitysingular)->first()->id:NULL;
        else:
            $n = NULL;
        endif;
        UserProfiles::create([
            'user_id' => $user['id'],
            'first_name' => $name[0],
            'last_name' => (count($name)>= 2)?$name[1]:'',
            'date_of_birth' => date('Y-m-d', strtotime($data[2])),
            'gender' => (strtolower($data[3]) == 'male')?2:((strtolower($data[3]) == 'female')?1:0),
            'nationality_id' => $n,
            'country_id' => 252
        ]);

        UserDocuments::create([
            'user_id' => $user['id'],
            'insurance_id' => 1,
        ]);
    }

    private function userCodeGenerator($date = null)
    {
        $user = Users::whereYear('created_at', date('Y', time()))->latest()->get()->first();
        if($user):
            $linv = substr($user->username, 3);
            $counter = intval($linv)+1;
        else: $counter = 1; endif;
        $number = 'S'.date('y').str_pad($counter, 4, "0",STR_PAD_LEFT);
        return $number;
    }
}
