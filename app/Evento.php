<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

use Yajra\Oci8\Eloquent\OracleEloquent as Model;

class Evento extends Model
{
    public $timestamps = false;

    // use SoftDeletes;
    protected  $table = "S_EVENTOS";

    protected $fillable = ['S_EVENTO_S_DESCRICAO','S_EVENTO_B_SRC'];

    protected $guarded = ['S_EVENTO_I_ID','S_CARROS_S_CARRO_I_ID'];

    // Definindo a chave primaria da tabela
    protected $primaryKey = 'S_EVENTO_I_ID';

    // public $dates = [
    //     'S_EVENTO_D_CREATED_AT',
    //     'S_EVENTO_D_UPDATED_AT',
    //     'S_EVENTO_D_DELETED_AT'
    // ];
}
