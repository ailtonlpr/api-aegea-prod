<?php

namespace App;

use Yajra\Oci8\Eloquent\OracleEloquent as Model;

class Registro extends Model
{
    public $timestamps = false;

    // use SoftDeletes;
    protected  $table = "S_REGISTROS";

    protected $fillable = ['S_REGISTRO_S_MOTORISTA','S_REGISTRO_S_ENDERECO', 'S_REGISTRO_D_DATA_INC', 'S_REGISTRO_D_DATA_POS', 'S_REGISTRO_S_LATITUDE', 'S_REGISTRO_S_LONGITUDE'];

    protected $guarded = ['S_REGISTRO_I_ID','S_CARROS_S_CARRO_I_ID'];

    // Definindo a chave primaria da tabela
    protected $primaryKey = 'S_REGISTRO_I_ID';
}