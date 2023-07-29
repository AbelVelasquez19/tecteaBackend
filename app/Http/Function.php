<?php

function exec_query($nombrestore,$arraydatos)
{		
    $caddatos = '';
    if($arraydatos != '' || $arraydatos != null){
        if(count($arraydatos) > 0){
            for($i=0;$i<count($arraydatos);$i++){
                    $nomvar = $arraydatos[$i][0];
                    $valvar = $arraydatos[$i][1];					
                $caddatos.= $nomvar."='".$valvar."',";
            }
            $caddatos = substr($caddatos,0,strlen($caddatos)-1);
        }
    }
    $cadins = 'EXEC '.$nombrestore.' '.$caddatos;
    try {
        $query = DB::select($cadins);
        return $query;
    } catch (Exception $e) {
        return $e;
    }
}