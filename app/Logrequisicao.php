<?php

namespace App;

use Yajra\Oci8\Eloquent\OracleEloquent as Model;

class Logrequisicao extends Model
{    
    public $timestamps = false;

    protected $table = "S_LOG_REQUISICAO"; // Definição da tabela

    // Quem estou terminando gravar em massa
    protected $fillable = ['DATA','JSON','IP','METODO'];

    // Este campo não vai na controller
    protected $guarded = ['ID_REQUISICAO'];

    // Definindo a chave primaria da tabela
    protected $primaryKey = 'ID_REQUISICAO';
    
}