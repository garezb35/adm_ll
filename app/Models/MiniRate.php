<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniRate extends Model
{
    use HasFactory;

    protected $table = 'sphinx.mn_rate';
    public $timestamps = false;

    public function pwb()
    {
        return $this->hasOne(MiniRatePwb::class, 'pwbid');
    }

    public function psa()
    {
        return $this->hasOne(MiniRatePsa::class, 'psaid');
    }

    public function up1()
    {
        return $this->hasOne(MiniRateUP1::class, 'up1id', 'up1id');
    }

    public function up3()
    {
        return $this->hasOne(MiniRateUP3::class, 'up3id', 'up3id');
    }

    public function up5()
    {
        return $this->hasOne(MiniRateUP5::class, 'up5id', 'up5id');
    }

    public function cs3()
    {
        return $this->hasOne(MiniRateCS3::class, 'cs3id', 'cs3id');
    }

    public function cs5()
    {
        return $this->hasOne(MiniRateCS5::class, 'cs5id', 'cs5id');
    }

    public function cp3()
    {
        return $this->hasOne(MiniRateCP3::class, 'cp3id', 'cp3id');
    }

    public function cp5()
    {
        return $this->hasOne(MiniRateCP5::class, 'cp5id', 'cp5id');
    }

    public function bbp()
    {
        return $this->hasOne(MiniRateBBP::class, 'bbpid', 'bbpid');
    }

    public function bbs()
    {
        return $this->hasOne(MiniRateBBS::class, 'bbsid', 'bbsid');
    }

    public function hsp3()
    {
        return $this->hasOne(MiniRateHSP3::class, 'hsp3id', 'hsp3id');
    }

    public function hss3()
    {
        return $this->hasOne(MiniRateHSS3::class, 'hss3id', 'hss3id');
    }

    public function hsp5()
    {
        return $this->hasOne(MiniRateHSP5::class, 'hsp5id', 'hsp5id');
    }

    public function hss5()
    {
        return $this->hasOne(MiniRateHSS5::class, 'hss5id', 'hss5id');
    }

    public function eos5()
    {
        return $this->hasOne(MiniRateEOS5::class, 'eos5id', 'eos5id');
    }

    public function eos3()
    {
        return $this->hasOne(MiniRateEOS3::class, 'eos3id', 'eos3id');
    }

    public function keno()
    {
        return $this->hasOne(MiniRateKeno::class, 'kenoid', 'kenoid');
    }

    public function klayp5()
    {
        return $this->hasOne(MiniRateKlayp5::class, 'klayp5id', 'klayp5id');
    }

    public function klays5()
    {
        return $this->hasOne(MiniRateKlays5::class, 'klays5id', 'klays5id');
    }

    public function klayp2()
    {
        return $this->hasOne(MiniRateKlayp2::class, 'klayp2id', 'klayp2id');
    }

    public function klays2()
    {
        return $this->hasOne(MiniRateKlays2::class, 'klays2id', 'klays2id');
    }

    public function mtp3()
    {
        return $this->hasOne(MiniRateMtp3::class, 'mtp3id', 'mtp3id');
    }

    public function mts3()
    {
        return $this->hasOne(MiniRateMts3::class, 'mts3id', 'mts3id');
    }

    public function mtp5()
    {
        return $this->hasOne(MiniRateMtp5::class, 'mtp5id', 'mtp5id');
    }

    public function mts5()
    {
        return $this->hasOne(MiniRateMts5::class, 'mts5id', 'mts5id');
    }

    public function xrp3()
    {
        return $this->hasOne(MiniRateXrp3::class, 'xrp3id', 'xrp3id');
    }

    public function xrp5()
    {
        return $this->hasOne(MiniRateXrp5::class, 'xrp5id', 'xrp5id');
    }

    public function xrs3()
    {
        return $this->hasOne(MiniRateXrs3::class, 'xrs3id', 'xrs3id');
    }

    public function xrs5()
    {
        return $this->hasOne(MiniRateXrs5::class, 'xrs5id', 'xrs5id');
    }

    public function dhpwb()
    {
        return $this->hasOne(MiniRateDHPwb::class, 'dhpwbid', 'dhpwbid');
    }

    public function dhpsa()
    {
        return $this->hasOne(MiniRateDHPsa::class, 'dhpsaid', 'dhpsaid');
    }

}
