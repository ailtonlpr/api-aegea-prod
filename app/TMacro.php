<?php

namespace App;

use Yajra\Oci8\Eloquent\OracleEloquent as Model;

class TMacro extends Model
{
    public $timestamps = false;

    protected  $table = "T_S_MACROS";

    protected $fillable = ['S_MACRO_S_DESCRICAO','S_MACRO_T_APR_PROC'];

    protected $guarded = ['S_MACRO_I_ID','S_CARROS_S_CARRO_I_ID','S_CARROS_S_CARRO_I_ID_INTERNO','S_CARROS_S_CARRO_S_PLACA'];

    // Definindo a chave primaria da tabela
    protected $primaryKey = 'S_MACRO_I_ID';
}