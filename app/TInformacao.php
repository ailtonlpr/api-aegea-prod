<?php

namespace App;

use Yajra\Oci8\Eloquent\OracleEloquent as Model;

class TInformacao extends Model
{
    public $timestamps = false;

    protected  $table = "T_S_INFORMACOES";

    protected $fillable = ['S_INFORMACOE_F_ODO','S_INFORMACOE_I_ODO
    _TOTAL', 'S_INFORMACOE_I_RPM', 'S_INFORMACOE_I_VELOCIDADE', 'S_INFORMACOE_B_LOG', 'S_INFORMACOE_B_IGN', 'S_INFORMACOE_B_GPS'];

    protected $guarded = ['S_INFORMACOE_I_ID','S_CARROS_S_CARRO_I_ID','S_CARROS_S_CARRO_I_ID_INTERNO','S_CARROS_S_CARRO_S_PLACA'];

    // Definindo a chave primaria da tabela
    protected $primaryKey = 'S_INFORMACOE_I_ID';

}