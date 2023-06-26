<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteDomain extends Model
{
    use HasFactory;

    protected $table = 'sphinx.site_domain';

    public $timestamps = false;
}
