<?php

namespace App;

use Yajra\Oci8\Eloquent\OracleEloquent as Model;

class TEvento extends Model
{
    public $timestamps = false;

    protected  $table = "T_S_EVENTOS";

    protected $fillable = ['S_EVENTO_S_DESCRICAO','S_EVENTO_B_SRC'];

    protected $guarded = ['S_EVENTO_I_ID','S_CARROS_S_CARRO_I_ID','S_CARROS_S_CARRO_I_ID_INTERNO','S_CARROS_S_CARRO_S_PLACA'];

    // Definindo a chave primaria da tabela
    protected $primaryKey = 'S_EVENTO_I_ID';
}