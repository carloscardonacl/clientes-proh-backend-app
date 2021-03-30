<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Configuracion extends Model
{
    use HasFactory;
    protected $table = 'Configuracion';
    protected $primaryKey = 'Id_Configuracion';
    public $timestamps = false;

    
     function prefijoConsecutivo($index){
        $prop = "Prefijo_$index";        
        return  $this->$prop;
    }   

     function guardarConsecutivoConfig($index,$consecutivo){
        
        $this->$index = $consecutivo+=1;
        $this->save();
     
    }

     function getConsecutivo($mod,$tipo_consecutivo) {
        // sleep(strval( rand(2, 8)));
         $query = "SELECT MAX(N.Codigo) AS Codigo FROM ( SELECT Codigo FROM $mod ORDER BY Id_$mod DESC LIMIT 10 )N ";
         $res = DB::select($query);
         $res = $res[0];
       

        $prefijo = $this->prefijoConsecutivo($tipo_consecutivo);
        $NumeroCodigo=substr($res->Codigo,strlen($prefijo)); 
       
        $NumeroCodigo += 1;
        
        $cod = $prefijo . $NumeroCodigo ;
        
        $query = "SELECT Id_$mod AS ID FROM $mod WHERE Codigo = '$cod'";
      
        $res2 = DB::select($query);
        
        $res2 = $res2 ? $res2[0] : $res2;
        if($res2){  
            sleep(strval(rand(0,3)));
            $this->getConsecutivo($mod,$tipo_consecutivo);
        }

        $this->guardarConsecutivoConfig($tipo_consecutivo,$NumeroCodigo);

        return $cod;
    }

     function Consecutivo($index){
        $num_cotizacion=$this->$index;
      
        $consecutivo = number_format((INT) $this->$index,0,"","");
        $this->$index = $consecutivo+1;
        $this->save();
     
        $d = "Prefijo_".$index; 
        $cod = $this->$d;
        $cod.sprintf("%05d", $num_cotizacion);

        return $cod;
    } 

}
