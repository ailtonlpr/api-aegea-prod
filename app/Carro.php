<?php

namespace App;

use Yajra\Oci8\Eloquent\OracleEloquent as Model;

class Carro extends Model
{    
    public $timestamps = false;

    protected $table = "S_CARROS"; // Definição da tabela

    // Quem estou terminando gravar em massa
    protected $fillable = ['S_CARRO_S_PLACA','S_CARRO_I_NUMERO_SERIAL','S_CARRO_I_ID_INTERNO'];

    // Este campo não vai na controller
    protected $guarded = ['S_CARRO_I_ID'];

    // Definindo a chave primaria da tabela
    protected $primaryKey = 'S_CARRO_I_ID';

    /*
    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {
            $dt = date('d-M-y');
            $model->s_carro_d_created_at = "TO_DATE('$dt', 'yyyy/mm/dd hh24:mi:ss')";
            return true;
        });

        static::updating(function($model) {
         // $dt = new DateTime;
         // $model->s_carro_d_updated_at = $dt->format('m-d-y H:i:s');
         // return true;

            $dt = date('d-M-y');
            $model->s_carro_d_updated_at = "TO_DATE('$dt', 'yyyy/mm/dd hh24:mi:ss')";
            return true;

        });

        static::deleting(function($model) {
            $dt = date('d-M-y');
            $model->s_carro_d_deleted_at = "TO_DATE('$dt', 'yyyy/mm/dd hh24:mi:ss')";
            return true;
        });
    }
    */    
}