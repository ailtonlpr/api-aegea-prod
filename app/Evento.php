<?php

namespace App;

use Yajra\Oci8\Eloquent\OracleEloquent as Model;

class Evento extends Model
{
    public $timestamps = false;

    protected  $table = "S_EVENTOS";

    protected $fillable = ['S_EVENTO_S_DESCRICAO','S_EVENTO_B_SRC'];

    protected $guarded = ['S_EVENTO_I_ID','S_CARROS_S_CARRO_I_ID'];

    // Definindo a chave primaria da tabela
    protected $primaryKey = 'S_EVENTO_I_ID';
}