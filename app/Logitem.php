<?php

namespace App;

use Yajra\Oci8\Eloquent\OracleEloquent as Model;

class Logitem extends Model
{    
    public $timestamps = false;

    protected $table = "S_LOG_ITEM"; // Definição da tabela

    // Quem estou terminando gravar em massa
    protected $fillable = ['DATA','JSON','ID_INTERNO'];

    // Este campo não vai na controller
    protected $guarded = ['ID_ITEM'];

    // Definindo a chave primaria da tabela
    protected $primaryKey = 'ID_ITEM';
    
}