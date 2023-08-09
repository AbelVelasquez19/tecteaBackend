<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Return_;

class AccessController extends Controller
{
   private $apiToken;
   public function __construct(){
      $this->apiToken = uniqid(base64_encode(Str::random(60)));
   }

   private static  function selectUser($OPTION, $USUA_CODIGO){
      $CMD[] = array('@OPTION',$OPTION);
      $CMD[] = array('@USUA_CODIGO',$USUA_CODIGO);
      $READ = exec_query("ACCESOS.SP_LOGIN",$CMD);
      return $READ;
   }

   public function login(Request $request){
      $rules =[
         'USUA_USERNAME'=>'required|email',
         'USUA_PASSWORD'=>'required|min:8',
      ];
      $mensaje=[
         'USUA_USERNAME.required' => 'Su correo electrónico es requerido.',
         'USUA_USERNAME.email'=>'El formato de su correo electrónico es invalido.',
         'USUA_PASSWORD.required'=>'Por favor escriba una contraseña.',
         'USUA_PASSWORD.min'=>'La contraseña debe tener al menos 8 caracteres.',
     ];
     $validator = Validator::make($request->all(),$rules,$mensaje);
      if($validator->fails()):
         return response()->json(['STATUS'=>0,'ERROR' => $validator->errors()->toArray()]);
      else:
         $USUA_USERNAME = e($request->input('USUA_USERNAME'));
         $USUA_PASSWORD = e($request->input('USUA_PASSWORD'));
         unset($CMD);
         $CMD[] = array('@OPTION',1);
         $CMD[] = array('@USUA_USERNAME',$USUA_USERNAME);
         $CMD[] = array('@USUA_PASSWORD',$USUA_PASSWORD);
         $READ = exec_query("ACCESOS.SP_LOGIN",$CMD);
         if(intval(count($READ)) > 0){
            unset($CMD);
            $CMD[] = array('@OPTION',2);
            $CMD[] = array('@USEA_TOKEN',$this->apiToken);
            $CMD[] = array('@USUA_CODIGO',$READ[0]->USUA_CODIGO);
            $READ_RES = exec_query("ACCESOS.SP_LOGIN",$CMD);
            if(intval(count($READ_RES)) > 0){
               return response()->json(['DATA'=> $READ_RES[0]],200);
            }else{
               $ERROR = array(
                  "MENSSAJE" => "No autorizado",
               );
               return response()->json(["STATUS" => 0, 'ERROR' => 'No autorizado']); 
            }
         }else{
            $ERROR = array(
               "MENSSAJE" => "Usuario y/o contraseña incorrecto",
            );
            return response()->json(["STATUS" => 0, "ERROR"=>$ERROR]); 
         }
		endif;
   }

   public function createUser(Request $request){
      $rules =[
         'PERS_DOCUMENTO' => 'required|min:8|max:8',
         "PERS_NOMCOM" =>'required',
         'USUA_USERNAME'=>'required|email',
         'USUA_PASSWORD'=>'required|min:8',
      ];
      $mensaje=[
         'PERS_DOCUMENTO.required'=>'Su N° DNI es requerido.',
         'PERS_DOCUMENTO.min'=>'DNI debe tener minimo 8 caracteres.',
         'PERS_DOCUMENTO.max'=>'DNI debe tener maximo 8 caracteres.',
         'PERS_NOMCOM.required' => 'Debe ingresar nombre completo.',
         'USUA_USERNAME.required' => 'Su correo electrónico es requerido.',
         'USUA_USERNAME.email'=>'El formato de su correo electrónico es invalido.',
         'USUA_PASSWORD.required'=>'Por favor escriba una contraseña.',
         'USUA_PASSWORD.min'=>'La contraseña debe tener al menos 8 caracteres.',
     ];
     $validator = Validator::make($request->all(),$rules,$mensaje);
      if($validator->fails()):
         return response()->json(['STATUS'=>0,'ERROR' => $validator->errors()->toArray()]);
      else:
         $PERF_CODIGO = 01;
         $PERS_DOCUMENTO = e($request->input('PERS_DOCUMENTO'));
         $PERS_NOMCOM = e($request->input('PERS_NOMCOM'));
         $USUA_USERNAME = e($request->input('USUA_USERNAME'));
         $USUA_PASSWORD = e($request->input('USUA_PASSWORD'));
         $AREA_CODIGO = 01;
         $USEA_TOKEN = $this->apiToken;

         unset($CMD);
         $CMD[] = array('@OPTION',5);
         $CMD[] = array('@PERS_DOCUMENTO',$PERS_DOCUMENTO);
         $CMD[] = array('@PERS_NOMCOM',$PERS_NOMCOM);
         $READ = exec_query("ACCESOS.SP_LOGIN",$CMD);
         $PERS_CODIGO = $READ[0]->RESULT;
         $STATUS = $READ[0]->STATUS;
         if(intval($STATUS) == 1 || intval($STATUS) == 2){
            unset($CMD);
            $CMD[] = array('@OPTION',4);
            $CMD[] = array('@PERF_CODIGO',$PERF_CODIGO);
            $CMD[] = array('@USUA_USERNAME',$USUA_USERNAME);
            $CMD[] = array('@USUA_PASSWORD',$USUA_PASSWORD);
            $CMD[] = array('@PERS_CODIGO',$PERS_CODIGO);
            $CMD[] = array('@AREA_CODIGO',$AREA_CODIGO);
            $CMD[] = array('@USEA_TOKEN',$USEA_TOKEN);
            $READ = exec_query("ACCESOS.SP_LOGIN",$CMD);
            return response()->json(['DATA'=> $READ[0]],200);
         }
      endif;
      //falta validacion
   }
}
