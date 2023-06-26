<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteAdminIplist extends Model
{
    use HasFactory;

    protected $table = 'sphinx.site_admin_iplist';

    protected $fillable = ['id', 'userid', 'ip_addr'];

}
