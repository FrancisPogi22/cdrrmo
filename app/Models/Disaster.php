<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disaster extends Model
{
    use HasFactory;

    protected $table = 'disaster';

    protected $primaryKey = 'disaster_id';

    protected $guarded = [];

    protected $fillable = [
        'disaster_id',
        'disaster_name',
    ];

    public $timestamps = false;

    public function displayDisasterObject(){
        return $this->all()->sortBy('disaster_id');
    }

    public function registerDisasterObject($disaster){
        return $this->create($disaster);
    }

    public function updateDisasterObject($request, $disasterId){

        $disasterData = [
            'disaster_name' => Str::ucfirst(trim($request->input('disaster_name'))),
        ];

        $disaster = $this->find($disasterId);
        $disaster->update($disasterData);
    }

    public function removeDisasterObject($disaster){
        $disaster = $this->find($disaster);
        $disaster->delete();
    }
}