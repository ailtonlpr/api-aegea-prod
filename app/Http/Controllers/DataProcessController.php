<?php

namespace App\Http\Controllers;

use App\Carro;
use App\Evento;
use App\Infocan;
use App\Informacao;
use App\Macro;
use App\Registro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use DB;

class DataProcessController extends Controller
{
    
    /*
    public function __construct(Request $request) 
    {
        // echo '<pre>';
        // print_r($request->positions);
        // die('primeiro');
    }
    */

    public function getIP()
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"]))
         $ip = $_SERVER["HTTP_CLIENT_IP"];
        elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
         $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else
         $ip = $_SERVER["REMOTE_ADDR"];

        return $ip;
    }

    public function getCargaLog($request)
    {
        $nome_arquivo = "log_de_carga_".date('Y-m-d').".log";

        // Existem a pasta
        if(!Storage::disk('api_public_log')->exists('/cargas')) {
            Storage::disk('api_public_log')->makeDirectory('/cargas', 0775, true); //creates directory
        }
        // Gravar o arquivo
        Storage::disk('api_public_log')->append('/cargas/'.$nome_arquivo, date('Y-m-d H:i:s'));
        Storage::disk('api_public_log')->append('/cargas/'.$nome_arquivo, $this->getIP($_SERVER));
        Storage::disk('api_public_log')->append('/cargas/'.$nome_arquivo, $_SERVER['REQUEST_METHOD']);
        Storage::disk('api_public_log')->append('/cargas/'.$nome_arquivo, $request);
        
        Log::debug($request);
    }
    

    public function aviso()
    {
        return response()->json(
            [
                'code' => 200,
                'msg' => "Use o metodo post"
            ]
        );
    }

    public function process(Request $request)
    {

        // echo '<pre>';
        // print_r($request->positions);
        // die('aki');
        // return $request;

        // select seq_s_carros.nextval from dual;
       // die('aki');
       // $prox_id = DB::connection('oracle')->selectOne("select seq_s_carros.nextval from dual");
       // $prox_id = DB::connection('oracle')->selectOne("select seq_s_carros.nextval from dual");
       // echo $prox_id->nextval;

       die('aki');

        $this->getCargaLog($request);

        if (isset($request->positions))
        {
            
            foreach ($request->positions as $data){
                
                $carro = DB::connection('oracle')
                    ->table('s_carros')
                    ->select('s_carro_i_id','s_carro_s_placa','s_carro_i_numero_serial','s_carro_i_id_interno','s_carro_d_created_at','s_carro_d_updated_at','s_carro_d_deleted_at')
                    ->where('s_carro_s_placa', $data['placa'])
                    ->first();
                
                try {
                    \DB::beginTransaction();
                    if(!$carro) {
                        
                        $agora = date('d-m-Y');
                        DB::setDateFormat('DD-MM-YYYY');                       

                        // $prox_id = DB::connection('oracle')->table('s_carros')->select('s_carro_i_id')->max('s_carro_i_id');
                        // $prox_id = $prox_id + 1;

                        $_prox_id = DB::connection('oracle')->selectOne("select seq_s_carros.nextval from dual");
                        $prox_id = $_prox_id->nextval;

                        $carro = new Carro();
                        $carro->s_carro_i_id = $prox_id;
                        $carro->s_carro_s_placa = $data['placa'];
                        $carro->s_carro_i_numero_serial = $data['serialNumber'];
                        $carro->s_carro_i_id_interno = $data['id'];
                        $carro->s_carro_d_created_at = $agora;

                        if(!$carro->save()){
                            throw new \Exception('Erro ao gravar os dados na tabela Carros.');
                        }
                    }
                    

                    $this->gravarEvento($carro->s_carro_i_id, $data);
                    $this->gravarInfocan($carro->s_carro_i_id, $data);
                    $this->gravarInformacao($carro->s_carro_i_id, $data);
                    $this->gravarMacro($carro->s_carro_i_id, $data);
                    $this->gravarRegistro($carro->s_carro_i_id, $data);
                    $this->gerarArquivo($data);

                } catch (\Exception $e) {
                    \DB::rollback();

                    $this->gerarLogErro($data, $e->getMessage(), $e->getCode());
                    
                    return response()->json(
                        [
                            'code' => 500,
                            'msg' => "Erro na gravação dos dados.",
                            'erroMsg' => $e->getMessage(),
                            'erroCode' => $e->getCode(),
                        ]
                    );
                } finally {
                    \DB::commit();
                }
                
            }//foreach

            return response()->json(
                [
                    'code' => 200,
                    'msg' => "Gravada com sucesso!"
                ]
            );
        } 
        else 
        {
            $this->gerarLog();
            return response()->json(
                [
                    'code' => 200,
                    'msg' => "sem dados"
                ]
            );
        }
    }//process

    private function gerarLog()
    {
        try
        {
            $nome_file = 'log'.'-'.date('Y_m_d_H_i_s').'.txt';
            $data = [
                    'code' => 200,
                    'msg' => "sem dados"
                ];
            Storage::disk('api_public')->append($nome_file, json_encode($data));
            Storage::disk('api_public')->move($nome_file, '/api_storage/logsis/'.$nome_file."");
        }
        catch (\Exception $e)
        {
            throw new \Exception('Erro ao criar arquivo {$nome_file}');
        }
    }

    private function gerarLogErro($data, $getMessage, $getCode )
    {
        try
        {
            $nome_file = 'log_err'.'-'.date('Y_m_d_H_i_s').'.txt';
            $dados = [
                'code'=> $getCode,
                'mensage'=>$getMessage,
                'dados'=>$data
            ];

            Storage::disk('api_public')->append($nome_file, json_encode($dados));
            Storage::disk('api_public')->move($nome_file, '/api_storage/erro/'.$nome_file."");
        }
        catch (\Exception $e)
        {
            throw new \Exception('Erro ao criar arquivo {$nome_file}');
        }
    }


    private function gerarArquivo($data)
    {
        try
        {
            $nome_file = $data['placa'].'-'.$data['id'].'-'.date('Y_m_d_H_i_s').'.json';
            Storage::disk('api_public')->append($nome_file, json_encode($data));
            Storage::disk('api_public')->move($nome_file, '/api_storage/log/'.$nome_file."");
        }
        catch (\Exception $e)
        {
            throw new \Exception('Erro ao criar arquivo {$nome_file}');
        }
    }

    private function gravarEvento($carro_id, $data)
    {       
        foreach ($data['eventos'] as $d){
            $agora = date('d-m-Y');
            DB::setDateFormat('DD-MM-YYYY');
            
            // $prox_id = DB::connection('oracle')->table('s_eventos')->select('s_evento_i_id')->max('s_evento_i_id');
            // $prox_id = $prox_id + 1;

            $_prox_id = DB::connection('oracle')->selectOne("select seq_s_eventos.nextval from dual");
            $prox_id = $_prox_id->nextval;

            $evento = new Evento();
            $evento->s_evento_i_id = $prox_id;
            $evento->s_carros_s_carro_i_id = $carro_id;
            $evento->s_carros_s_carro_i_id_interno = $data['id'];
            $evento->s_carros_s_carro_s_placa = $data['placa'];
            $evento->s_evento_s_descricao = $d['desc'];
            $evento->s_evento_b_src = $d['src'];
            $evento->s_evento_d_created_at = $agora;
            // $evento->s_evento_d_updated_at = null;
            // $evento->s_evento_d_deleted_at = null;

            if(!$evento->save()){
                throw new \Exception('Erro ao gravar os dados na tabela Eventos. ID carro -> {$carro_id}');
            }
        }
    }//gravarEvento

    private function gravarInfocan($carro_id, $data)
    {
        $agora = date('d-m-Y');
        DB::setDateFormat('DD-MM-YYYY');
        
        // $prox_id = DB::connection('oracle')->table('s_inforcans')->select('s_inforcan_i_id')->max('s_inforcan_i_id');
        // $prox_id = $prox_id + 1;

        $_prox_id = DB::connection('oracle')->selectOne("select seq_s_inforcans.nextval from dual");
        $prox_id = $_prox_id->nextval;
        
        $data_ = $data['can'];

        $infocan = new Infocan();
        $infocan->s_inforcan_i_id = $prox_id;
        $infocan->s_carros_s_carro_i_id = $carro_id;
        $infocan->s_carros_s_carro_i_id_interno = $data['id'];
        $infocan->s_carros_s_carro_s_placa = $data['placa'];
        $infocan->s_inforcan_f_combustivel = $data_['comb'];
        $infocan->s_inforcan_b_cinto = $data_['cinto'];
        $infocan->s_inforcan_b_freio = $data_['freio'];
        $infocan->s_inforcan_b_limp = $data_['limp'];
        $infocan->s_inforcan_d_created_at = $agora;
        // $infocan->s_inforcan_d_updated_at = null;
        // $infocan->s_inforcan_d_deleted_at = null;

        if(!$infocan->save()){
            throw new \Exception('Erro ao gravar os dados na tabela Infocan. ID carro -> {$carro_id}');
        }

    }//gravarInfocan

    private function gravarInformacao($carro_id, $data)
    {
        $agora = date('d-m-Y');
        DB::setDateFormat('DD-MM-YYYY');
        
        // $prox_id = DB::connection('oracle')->table('s_informacoes')->select('s_informacoe_i_id')->max('s_informacoe_i_id');
        // $prox_id = $prox_id + 1;

        $_prox_id = DB::connection('oracle')->selectOne("select seq_s_informacoes.nextval from dual");
        $prox_id = $_prox_id->nextval;
        
        $data_ = $data['info'];

        $informacao = new Informacao();
        $informacao->s_informacoe_i_id = $prox_id;
        $informacao->s_carros_s_carro_i_id = $carro_id;
        $informacao->s_carros_s_carro_i_id_interno = $data['id'];
        $informacao->s_carros_s_carro_s_placa = $data['placa'];
        $informacao->s_informacoe_f_odo = $data_['odo'];
        $informacao->s_informacoe_f_odo_total = $data_['odoTotal'];
        $informacao->s_informacoe_i_rpm = $data_['rpm'];
        $informacao->s_informacoe_i_velocidade = $data_['vel'];
        $informacao->s_informacoe_b_log = $data_['log'];
        $informacao->s_informacoe_b_ign = $data_['ign'];
        $informacao->s_informacoe_b_gps = $data_['gps'];
        $informacao->s_informacoe_d_created_at = $agora;
        // $informacao->s_informacoe_d_updated_at = null;
        // $informacao->s_informacoe_d_deleted_at = null;

        if(!$informacao->save()){
            throw new \Exception('Erro ao gravar os dados na tabela Informacoes. ID carro -> {$carro_id}');
        }
    }//gravarInformacao

    private function gravarMacro($carro_id, $data)
    {
        foreach ($data['macros'] as $d){

            $agora = date('d-m-Y');
            DB::setDateFormat('DD-MM-YYYY');
            
            // $prox_id = DB::connection('oracle')->table('s_macros')->select('s_macro_i_id')->max('s_macro_i_id');
            // $prox_id = $prox_id + 1;

            $_prox_id = DB::connection('oracle')->selectOne("select seq_s_macros.nextval from dual");
            $prox_id = $_prox_id->nextval;

            $macro = new Macro();
            $macro->s_macro_i_id = $prox_id;
            $macro->s_carros_s_carro_i_id = $carro_id;
            $macro->s_carros_s_carro_i_id_interno = $data['id'];
            $macro->s_carros_s_carro_s_placa = $data['placa'];
            $macro->s_macro_s_descricao = $d['desc'];
            $macro->s_macro_t_apr_proc = $d['aprProc'];
            $macro->s_macro_d_created_at = $agora;
            // $macro->s_macro_d_updated_at = null;
            // $macro->s_macro_d_deleted_at = null;

            if(!$macro->save()){
                throw new \Exception('Erro ao gravar os dados na tabela Macros. ID carro -> {$carro_id}');
            }
        }
    }//gravarMacro

    private function gravarRegistro($carro_id, $data)
    {
        $agora = date('d-m-Y');
        DB::setDateFormat('DD-MM-YYYY');
            
        // $prox_id = DB::connection('oracle')->table('s_registros')->select('s_registro_i_id')->max('s_registro_i_id');
        //$prox_id = $prox_id + 1;

        $_prox_id = DB::connection('oracle')->selectOne("select seq_s_registros.nextval from dual");
        $prox_id = $_prox_id->nextval;

        $registro = new Registro();
        $registro->s_registro_i_id = $prox_id;
        $registro->s_carros_s_carro_i_id = $carro_id;
        $registro->s_carros_s_carro_i_id_interno = $data['id'];
        $registro->s_carros_s_carro_s_placa = $data['placa'];
        $registro->s_registro_i_cpf_motorista = $data['motorista'];
        $registro->s_registro_s_endereco = '"'.$data['end'].'"';
        $registro->s_registro_d_data_inc = Carbon::parse($data['dInc'])->format('d-m-Y');
        $registro->s_registro_d_data_pos = Carbon::parse($data['dPos'])->format('d-m-Y');
        $registro->s_registro_s_latitude = $data['coord'][0];
        $registro->s_registro_s_longitude = $data['coord'][1];
        $registro->s_registro_d_created_at = $agora;
        // $registro->s_registro_d_updated_at = null;
        // $registro->s_registro_d_deleted_at = null;


        if(!$registro->save()){
            throw new \Exception('Erro ao gravar os dados na tabela Registros. ID carro -> {$carro_id}');
        }
    }//gravarRegistro
}