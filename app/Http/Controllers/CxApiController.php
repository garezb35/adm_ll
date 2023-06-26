<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Models\BSBetListDay;
use App\Models\BSGameCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CxApiController extends Controller
{
    protected $opkey;
    protected $domain;

    public function __construct()
    {
        $this->opkey = config('app.cx_key');
        // $this->domain = "http://smapi.enjoycx.com/";
        $this->domain = "https://api3.vedaapi.com/";
    }

    public function getRoundDetail($roundid)
    {
        $url = $this->domain.'log/get';
        $param = array(
            'opkey' => $this->opkey,
            'roundid'=> $roundid,
        );
        $param['hash'] = strtolower(md5($this->opkey. "|" . http_build_query($param)));
        $xml = Http::get($url, $param)
            ->body();
        $result = json_decode($xml,true);
        if (isset($result['result']) && $result['result'] == 1)
            return $result['data']['link'];
        return '';
    }
}
