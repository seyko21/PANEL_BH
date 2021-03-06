<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 02-10-2014 23:10:16 
* Descripcion : retornoInversionController.php
* ---------------------------------------
*/    

class retornoInversionController extends Controller{

    public function __construct() {
        $this->loadModel("retornoInversion");
    }
    
    public function index(){ 
        Obj::run()->View->render("indexRetornoInversion");
    }
    
    public function getConsulta(){ 
        Obj::run()->View->render("consultarROIOrden");
    }
        
    
    public function getGridRetornoInversion(){
        $consultar   = Session::getPermiso('CREINCC');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->retornoInversionModel->getRetornoInversion();
        
        $num = Obj::run()->retornoInversionModel->_iDisplayStart;
        if($num >= 10){
            $num++;
        }else{
            $num = 1;
        }
        
        if(!isset($rResult['error'])){  
            $iTotal         = isset($rResult[0]['total'])?$rResult[0]['total']:0;
            
            $sOutput = '{';
            $sOutput .= '"sEcho": '.intval($sEcho).', ';
            $sOutput .= '"iTotalRecords": '.$iTotal.', ';
            $sOutput .= '"iTotalDisplayRecords": '.$iTotal.', ';
            $sOutput .= '"aaData": [ ';     
            
            foreach ( $rResult as $aRow ){
                
                /*antes de enviar id se encrypta*/
                $idProducto = Aes::en($aRow['id_producto']);
                $idSocio  = Aes::en($aRow['id_persona']);

                                                                      
                 $c1 =$aRow['socio'];
                 $c2 =$aRow['codigos'];
                 $c3 =$aRow['ubicacion'];                                  
                 $c4 = number_format($aRow['inversion'],2);
                 $c5 = number_format($aRow['ingresos'],3);
                 $c6 = number_format($aRow['roi']*100,2);
                 $c7 =number_format($aRow['porcentaje_ganacia']*100).' %';  
                 
                $axion = '"<div class=\"btn-group\">';
                 
                if($consultar['permiso']){
                    if($aRow['ingresos'] > 0):
                        $axion .= '<button type=\"button\" class=\"'.$consultar['theme'].'\" title=\"'.$consultar['accion'].'\" onclick=\"retornoInversion.getConsulta(this,\''.$idProducto.'\',\''.$idSocio.'\',\''.$c1.'\')\">';
                        $axion .= '    <i class=\"'.$consultar['icono'].'\"></i>';
                        $axion .= '</button>';                        
                    else:
                        $axion .= '<button type=\"button\" class=\"'.$consultar['theme'].'\" title=\"'.$consultar['accion'].'\" disabled >';
                        $axion .= '    <i class=\"'.$consultar['icono'].'\"></i>';
                        $axion .= '</button>';  
                    endif;                                        
                }             
                
                $axion .= ' </div>" ';
                 
                /*registros a mostrar*/
                $sOutput .= '["'.($num++).'","'.$c1.'","'.$c2.'","'.$c3.'","'.$c7.'","'.$c4.'","'.$c5.'","'.$c6.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
        
    public function getGridRoiOs(){        
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->retornoInversionModel->getGridRoiOs();
        
        $num = Obj::run()->retornoInversionModel->_iDisplayStart;
        if($num >= 10){
            $num++;
        }else{
            $num = 1;
        }
        
        if(!isset($rResult['error'])){  
            $iTotal         = isset($rResult[0]['total'])?$rResult[0]['total']:0;
            
            $sOutput = '{';
            $sOutput .= '"sEcho": '.intval($sEcho).', ';
            $sOutput .= '"iTotalRecords": '.$iTotal.', ';
            $sOutput .= '"iTotalDisplayRecords": '.$iTotal.', ';
            $sOutput .= '"aaData": [ ';     
            
            foreach ( $rResult as $aRow ){
                if($aRow['nro_cuotas'] < 0):
                    $cuota =1;          
                else:
                    $cuota =$aRow['nro_cuotas'];                     
                endif;
                
                switch ($aRow['estado']){
                    case 'R':
                        $estado = '<span class=\"label label-success\">'.CREIN_3.'</span>';
                        break;
                    case 'S':
                        $estado = '<span class=\"label label-warning\">'.CREIN_4.'</span>';
                        break;
                    case 'F':
                        $estado = '<span class=\"label label-info\">'.CREIN_5.'</span>';
                        break;
                    default:
                        $estado = '<span class=\"label label-success\">'.CREIN_6.'</span>';
                        break;                                  
                }
                
                $c1 =$aRow['orden_numero'];
                $c2 =$aRow['codigo'];
                $c3 =$aRow['fecha'];
                $c4 = Functions::convertirDiaMes($aRow['cantidad_mes']);
                $c5 = number_format($aRow['impuesto'],2);
                $c6 = number_format($aRow['importe'],2);
                $c7 = number_format($aRow['monto_total'],2);
                $c8 = number_format($aRow['comision_venta'],2);
                $c9 = number_format($aRow['egresos'],2);
                $c10 = number_format($aRow['total_utilidad'],2);                    
                $c11 = number_format($aRow['monto_utilidad'],2);
                $c12 = number_format((($aRow['monto_utilidad']/$cuota)*$aRow['pagados']),3);
                /*registros a mostrar*/
                $sOutput .= '["'.$c1.'","'.$c2.'","'.$c3.'","'.$c4.'","'.$c6.'","'.$c7.'","'.$c8.'","'.$c9.'","'.$c10.'","'.$c11.'","'.$c12.'","'.$estado.'"  ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }    
    
  
}

?>