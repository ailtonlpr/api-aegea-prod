<?php

namespace App;

use Yajra\Oci8\Eloquent\OracleEloquent as Model;

class TInfocan extends Model
{
    public $timestamps = false;

    protected  $table = "T_S_INFORCANS";

    protected $fillable = ['S_INFORCAN_F_COMBUSTIVEL','S_INFORCAN_B_CINTO', 'S_INFORCAN_B_FREIO', 'S_INFORCAN_B_LIMP'];

    protected $guarded = ['S_INFORCAN_I_ID','S_CARROS_S_CARRO_I_ID','S_CARROS_S_CARRO_I_ID_INTERNO','S_CARROS_S_CARRO_S_PLACA'];

    // Definindo a chave primaria da tabela
    protected $primaryKey = 'S_INFORCAN_I_ID';
}