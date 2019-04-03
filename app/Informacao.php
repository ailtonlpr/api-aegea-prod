<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

use Yajra\Oci8\Eloquent\OracleEloquent as Model;

class Informacao extends Model
{
    public $timestamps = false;

    // use SoftDeletes;
    protected  $table = "S_INFORMACOES";

    protected $fillable = ['S_INFORMACOE_F_ODO','S_INFORMACOE_I_ODO
    _TOTAL', 'S_INFORMACOE_I_RPM', 'S_INFORMACOE_I_VELOCIDADE', 'S_INFORMACOE_B_LOG', 'S_INFORMACOE_B_IGN', 'S_INFORMACOE_B_GPS'];

    protected $guarded = ['S_INFORMACOE_I_ID','S_CARROS_S_CARRO_I_ID'];

    // Definindo a chave primaria da tabela
    protected $primaryKey = 'S_INFORMACOE_I_ID';

    // public $dates = [
    //     'S_INFORMACOE_D_CREATED_AT',
    //     'S_INFORMACOE_D_UPDATED_AT',
    //     'S_INFORMACOE_D_DELETED_AT'
    // ];
}
