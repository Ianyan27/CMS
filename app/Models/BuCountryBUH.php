<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuCountryBUH extends Model
{
    use HasFactory;

    protected $table = 'bu_country_buh';

    protected $fillable = ['bu_id', 'country_id', 'buh_id'];

    // BU Country belongs to a single BU
    public function bu()
    {
        return $this->belongsTo(BU::class, 'bu_id');
    }

    // BU Country belongs to a single Country
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    // BU Country belongs to a single BUH
    public function buh()
    {
        return $this->belongsTo(BUH::class, 'buh_id');
    }

    // BU Country has many Sale Agents
    public function saleAgents()
    {
        return $this->hasMany(SaleAgent::class, 'bu_country_id');
    }
}
