<?php

namespace App\Http\Controllers;

use App\Evento;
use App\Infocan;
use App\Informacao;
use App\Macro;
use App\Registro;
use Illuminate\Http\Request;
use App\Carro;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use DB;

class DataProcessController extends Controller
{
    //
    public function process(Request $request)
    {
        
        $carros = DB::select('select * from S_CARROS');
        echo '<pre>';
        print_r($carros);
        die('humm');

        // echo '<pre>';
        // print_r($request);
        // print_r($request->positions);
        // die;

        foreach ($request->positions as $data){

            $carro = Carro::where('S_CARRO_S_PLACA', $data['placa'])->first();
            try{
                \DB::beginTransaction();
                if(!$carro) {

                    $carro = new Carro();
                    $carro->S_CARRO_S_PLACA = $data['placa'];
                    $carro->S_CARRO_I_NUMERO_SERIAL = $data['serialNumber'];
                    $carro->S_CARRO_I_ID_INTERNO = $data['id'];
                    $carro->S_CARRO_D_CREATED_AT = date('Y-m-d H:i:s');
                    $carro->S_CARRO_D_UPDATED_AT = null;
                    $carro->S_CARRO_D_DELETED_AT = null;
                    if(!$carro->save()){
                        throw new \Exception('Erro ao gravar os dados na tabela Carros.');
                    }
                }

                $this->gravarEvento($carro->S_CARRO_I_ID, $data);
                $this->gravarInfocan($carro->S_CARRO_I_ID, $data['can']);
                $this->gravarInformacao($carro->S_CARRO_I_ID, $data['info']);
                $this->gravarMacro($carro->S_CARRO_I_ID, $data);
                $this->gravarRegistro($carro->S_CARRO_I_ID, $data);
                $this->gerarArquivo($data);

            }catch (\Exception $e){
                \DB::rollback();
                return response()->json(
                    [
                        'code' => 500 ,
                        'msg' => "Erro na gravação dos dados.",
                        'erroMsg' => $e->getMessage(),
                        'erroCode' => $e->getCode(),
                    ]
                );
            }finally{
                \DB::commit();
            }
        }//foreach

        return response()->json(
            [
                'code' => 200,
                'msg' => "Gravada com sucesso!"
            ]
        );
    }//process

    private function gerarArquivo($data)
    {
        try
        {
            $nome_file = $data['placa'].'-'.$data['id'].'-'.date('Y_m_d_H_i_s').'.json';
            Storage::disk('api_public')->append($nome_file, json_encode($data));
            Storage::disk('api_public')->move($nome_file, '/api_storage/teste/'.$nome_file."");
        }
        catch (\Exception $e)
        {
            throw new \Exception('Erro ao criar arquivo {$nome_file}');
        }
    }

    private function gravarEvento($carro_id, $data)
    {
        foreach ($data['eventos'] as $d){

            $evento = new Evento();
            $evento->S_CARROS_S_CARRO_I_ID = $carro_id;
            $evento->S_EVENTO_S_DESCRICAO = $d['desc'];
            $evento->S_EVENTO_B_SRC = $d['src'];
            $evento->S_EVENTO_D_CREATED_AT = date('Y-m-d H:i:s');
            $evento->S_EVENTO_D_UPDATED_AT = null;
            $evento->S_EVENTO_D_DELETED_AT = null;

            if(!$evento->save()){
                throw new \Exception('Erro ao gravar os dados na tabela Eventos. ID carro -> {$carro_id}');
            }
        }
    }//gravarEvento

    private function gravarInfocan($carro_id, $data)
    {
        $infocan = new Infocan();
        $infocan->S_CARROS_S_CARRO_I_ID = $carro_id;
        $infocan->S_INFORCAN_F_COMBUSTIVEL = $data['comb'];
        $infocan->S_INFORCAN_B_CINTO = $data['cinto'];
        $infocan->S_INFORCAN_B_FREIO = $data['freio'];
        $infocan->S_INFORCAN_B_LIMP = $data['limp'];
        $infocan->S_INFORCAN_D_CREATED_AT = date('Y-m-d H:i:s');
        $infocan->S_INFORCAN_D_UPDATED_AT = null;
        $infocan->S_INFORCAN_D_DELETED_AT = null;

        if(!$infocan->save()){
            throw new \Exception('Erro ao gravar os dados na tabela Infocan. ID carro -> {$carro_id}');
        }

    }//gravarInfocan

    private function gravarInformacao($carro_id, $data)
    {
        $informacao = new Informacao();
        $informacao->S_CARROS_S_CARRO_I_ID = $carro_id;
        $informacao->S_INFORMACOE_F_ODO = $data['odo'];
        $informacao->S_INFORMACOE_F_ODO_TOTAL = $data['odoTotal'];
        $informacao->S_INFORMACOE_I_RPM = $data['rpm'];
        $informacao->S_INFORMACOE_I_VELOCIDADE = $data['vel'];
        $informacao->S_INFORMACOE_B_LOG = $data['log'];
        $informacao->S_INFORMACOE_B_IGN = $data['ign'];
        $informacao->S_INFORMACOE_B_GPS = $data['gps'];
        $informacao->S_INFORMACOE_D_CREATED_AT = date('Y-m-d H:i:s');
        $informacao->S_INFORMACOE_D_UPDATED_AT = null;
        $informacao->S_INFORMACOE_D_DELETED_AT = null;

        if(!$informacao->save()){
            throw new \Exception('Erro ao gravar os dados na tabela Informacoes. ID carro -> {$carro_id}');
        }
    }//gravarInformacao

    private function gravarMacro($carro_id, $data)
    {
        foreach ($data['macros'] as $d){

            $macro = new Macro();
            $macro->S_CARROS_S_CARRO_I_ID = $carro_id;
            $macro->S_MACRO_S_DESCRICAO = $d['desc'];
            $macro->S_MACRO_T_APR_PROC = $d['aprProc'];
            $macro->S_MACRO_D_CREATED_AT = date('Y-m-d H:i:s');
            $macro->S_MACRO_D_UPDATED_AT = null;
            $macro->S_MACRO_D_DELETED_AT = null;

            if(!$macro->save()){
                throw new \Exception('Erro ao gravar os dados na tabela Macros. ID carro -> {$carro_id}');
            }
        }
    }//gravarMacro

    private function gravarRegistro($carro_id, $data)
    {
        $registro = new Registro();
        $registro->S_CARROS_S_CARRO_I_ID = $carro_id;
        $registro->S_REGISTRO_S_MOTORISTA = $data['motorista'];
        $registro->S_REGISTRO_S_ENDERECO = $data['end'];
        $registro->S_REGISTRO_D_DATA_INC = Carbon::parse($data['dInc'])->format('Y-m-d H:m:s');
        $registro->S_REGISTRO_D_DATA_POS = Carbon::parse($data['dPos'])->format('Y-m-d H:m:s');
        $registro->S_REGISTRO_S_LATITUDE = $data['coord'][0];
        $registro->S_REGISTRO_S_LONGITUDE = $data['coord'][1];
        $registro->S_REGISTRO_D_CREATED_AT = date('Y-m-d H:i:s');
        $registro->S_REGISTRO_D_UPDATED_AT = null;
        $registro->S_REGISTRO_D_DELETED_AT = null;

        if(!$registro->save()){
            throw new \Exception('Erro ao gravar os dados na tabela Registros. ID carro -> {$carro_id}');
        }
    }//gravarRegistro
}

