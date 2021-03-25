<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;
    protected $table = 'Configuracion';
    protected $primaryKey = 'Id_Configuracion';


    
    function prefijoConsecutivo($index){
        $prop = "Prefijo_$index";
        $oItem =  $this->
        $prefijo= $this->$prop;

        
        return $prefijo;
    }   

    function guardarConsecutivoConfig($index,$consecutivo){

        $oItem = new complex('Configuracion','Id_Configuracion',1);
        $nc = $oItem->getData();
        $oItem->$index = $consecutivo+=1;
        $oItem->save();
        
        unset($oItem);
        
    }

    function getConsecutivo($mod,$tipo_consecutivo) {
        sleep(strval( rand(2, 8)));
    # $query = "SELECT  MAX(Codigo)  AS Codigo FROM $mod ";
        $query = "SELECT MAX(N.Codigo) AS Codigo FROM ( SELECT Codigo FROM $mod ORDER BY Id_$mod DESC LIMIT 10 )N ";

        $oCon = new consulta();
        $oCon->setQuery($query);
        $res = $oCon->getData();
        unset($oCon);
        
        $prefijo = $this->prefijoConsecutivo($tipo_consecutivo);
    
        $NumeroCodigo=substr($res['Codigo'],strlen($prefijo)); 
        $NumeroCodigo += 1;
        
        $cod = $prefijo . $NumeroCodigo ;

        $query = "SELECT Id_$mod AS ID FROM $mod WHERE Codigo = '$cod'";
        $oCon = new consulta();
        $oCon->setQuery($query);
        $res2 = $oCon->getData();
        unset($oCon);

        if($res2["ID"]){  
            sleep(strval(rand(0,3)));
            $this->getConsecutivo($mod,$tipo_consecutivo);
        }

        $this->guardarConsecutivoConfig($tipo_consecutivo,$NumeroCodigo);

        return $cod;
    }

    function Consecutivo($index){
        $oItem = new complex('Configuracion','Id_Configuracion',1);
        $nc = $oItem->getData();
        $consecutivo = number_format((INT) $oItem->$index,0,"","");
        $oItem->$index= $consecutivo+1;
        $oItem->save();
        $num_cotizacion=$nc[$index];
        unset($oItem);
        
        $cod = $nc["Prefijo_".$index].sprintf("%05d", $num_cotizacion);
        
        return $cod;
    } 

}
