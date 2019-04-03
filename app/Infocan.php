<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

use Yajra\Oci8\Eloquent\OracleEloquent as Model;

class Infocan extends Model
{
    public $timestamps = false;

    // use SoftDeletes;
    protected  $table = "S_INFORCANS";

    protected $fillable = ['S_INFORCAN_F_COMBUSTIVEL','S_INFORCAN_B_CINTO', 'S_INFORCAN_B_FREIO', 'S_INFORCAN_B_LIMP'];

    protected $guarded = ['S_INFORCAN_I_ID','S_CARROS_S_CARRO_I_ID'];

    // Definindo a chave primaria da tabela
    protected $primaryKey = 'S_INFORCAN_I_ID';

    // public $dates = [
    //     'S_INFORCAN_D_CREATED_AT',
    //     'S_INFORCAN_D_UPDATED_AT',
    //     'S_INFORCAN_D_DELETED_AT'
    // ];
}
