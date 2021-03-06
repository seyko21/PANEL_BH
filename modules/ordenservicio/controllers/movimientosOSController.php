<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 25-09-2014 23:09:14 
* Descripcion : movimientosOSController.php
* ---------------------------------------
*/    

class movimientosOSController extends Controller{

    public function __construct() {
        $this->loadModel("movimientosOS");
        $this->loadController(array('modulo'=>'ordenservicio','controller'=>'seguimientoPago'));   
    }
    
    public function index(){ 
        Obj::run()->View->render("indexMovimientosOS");
    }
    
    public function getFormConsulta(){ 
        Obj::run()->View->render("consultarMovimiento");
    }
    
    public function getGridMovimientosOS(){
        
        $consultar   = Session::getPermiso('MOVOSCC');
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->movimientosOSModel->getMovimientosOS();
        
        $num = Obj::run()->movimientosOSModel->_iDisplayStart;
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
                $encryptReg = Aes::en($aRow['id_ordenservicio']);
                
                /*
                 * configurando botones (add/edit/delete etc)
                 * se verifica si tiene permisos para editar
                 */
                $axion = '"<div class=\"btn-group\">';
                 
                if($consultar['permiso']){
                    $axion .= '<button type=\"button\" class=\"'.$consultar['theme'].'\" title=\"'.$consultar['accion'].'\" onclick=\"movimientosOS.getConsulta(this,\''.$encryptReg.'\' , \''.$aRow['orden_numero'].'\')\">';
                    $axion .= '    <i class=\"'.$consultar['icono'].'\"></i>';
                    $axion .= '</button>';
                }                

                $axion .= ' </div>" ';                                
                 switch($aRow['estado']){
                    case 'E':
                        $estado = '<span class=\"label label-default\">'.SEGCO_5.'</span>';
                        break;
                    case 'T':
                        $estado = '<span class=\"label label-success\">'.SEGPA_8.'</span>';
                        break;
                    case 'P':
                        $estado = '<span class=\"label label-warning\">'.SEGPA_7.'</span>';
                        break;
                    case 'A':
                        $estado = '<span class=\"label label-danger\">'.SEGPA_9.'</span>';
                        break;
                    case 'F':
                        $estado = '<span class=\"label label-info\">'.SEGPA_29.'</span>';
                        break;
                    case 'R':
                        $estado = '<span class=\"label bg-color-magenta txt-color-white\">'.SEGPA_30.'</span>';
                        break;
                }
                $m = number_format($aRow['monto_total'],2);
                $i = number_format($aRow['monto_impuesto'],2);
                $ig = number_format($aRow['ingresos'],2);
                $eg = number_format($aRow['egresos'],2);                
                $cv = number_format($aRow['comision_vendedor'],2);
                $ut1 = number_format($aRow['utilidad_principal'],2);
                $oi = number_format($aRow['otros_ingresos'],2);                
                $oe = number_format($aRow['otros_egresos'],2);
                $ut2 = number_format($aRow['utilidad_secundaria'],2);
                /*registros a mostrar*/
                $sOutput .= '["'.$aRow['orden_numero'].'","'.$aRow['fecha_contrato'].'","'.$m.'","'.$i.'","'.$ig.'","'.$eg.'","'.$cv.'","<b>'.$ut1.'</b>","'.$estado.'",'.$axion.' ';

                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    public function getGridMovInstalacion(){
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->movimientosOSModel->getMovInstalacion();
        
        $num = Obj::run()->movimientosOSModel->_iDisplayStart;
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
            
            foreach ( $rResult as $key=>$aRow ){
                /*antes de enviar id se encrypta*/
                $encryptReg = Aes::en($aRow['id_ordeninstalacion']);                
                
                /*registros a mostrar*/
                $sOutput .= '["'.$aRow['codigo'].'","'.$aRow['ordenin_numero'].'","'.$aRow['ubicacion'].' - '.$aRow['descripcion'].'","'.$aRow['fecha_instalacion'].'","S/.'.number_format($aRow['monto_total'],2).'" ';
                                
                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }
    
    public function getGridMovComision(){
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->movimientosOSModel->getMovComision();
        
        $num = Obj::run()->movimientosOSModel->_iDisplayStart;
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
            
            foreach ( $rResult as $key=>$aRow ){

                $c1 = 'S/.'.number_format($aRow['importe'],2);
                $c2 =  number_format($aRow['porcentaje_comision']*100,2).'%';
                $c3 = 'S/.'.number_format($aRow['comision_venta'],2);
        
                /*registros a mostrar*/
                $sOutput .= '["'.$aRow['codigo'].'","'.$aRow['ubicacion'].' - '.$aRow['descripcion'].'","'.  Functions::convertirDiaMes($aRow['cantidad_mes']).'","'.$c1.'","'.$c2.'","'.$c3.'" ';
                                
                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }    

    public function getGridMovIngresos(){
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->movimientosOSModel->getMovIngresos();
        
        $num = Obj::run()->movimientosOSModel->_iDisplayStart;
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
            
            foreach ( $rResult as $key=>$aRow ){
                /*antes de enviar id se encrypta*/                              
                /*registros a mostrar*/
                $c1 = $aRow['numero_cuota'];
                $c2 = 'S/.'.number_format($aRow['costo_mora'],2);
                $c3 = 'S/.'.number_format($aRow['monto_pago'],2);
                $c4 = $aRow['fecha_programada'];
                $c5 = $aRow['fecha_pagoreal'];
                switch($aRow['estado']){
                    case 'E':
                        $estado = '<span class=\"label label-default\" style=\"text-align:center;color:#fff\">'.CROPA_2.'</span>';
                        break;
                    case 'P':
                        $noDelete = 1;
                        $estado = '<span class=\"label label-success\" style=\"text-align:center;color:#fff\">'.CROPA_3.'</span>';
                        break;
                    case 'R':
                        $estado = '<span class=\"label label-warning\" style=\"text-align:center;color:#fff\">'.CROPA_4.'</span>';
                        break;
                    default:
                        $estado = '';
                        break;
                }
                $sOutput .= '["'.$c1.'","'.$c2.'","'.$c3.'","'.$c4.'","'.$c5.'","'.$estado.'" ';
                                
                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }    
    
    public function getGridMovUtilidad(){
        
        $sEcho          =   $this->post('sEcho');
        
        $rResult = Obj::run()->movimientosOSModel->getMovUtilidad();
        
        $num = Obj::run()->movimientosOSModel->_iDisplayStart;
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
            
            foreach ( $rResult as $key=>$aRow ){
        
                $c1 = $aRow['codigo'];
                $c2 = Functions::convertirDiaMes($aRow['cantidad_mes']);
                $c3 = 'S/.'.number_format($aRow['importe'],2);
                $c4 = 'S/.'.number_format($aRow['impuesto'],2);
                $c5 = 'S/.'.number_format($aRow['egresos'],2);
                $c6 = 'S/.'.number_format($aRow['utilidad'],2);
                /*registros a mostrar*/
                $sOutput .= '["'.$c1.'","'.$c2.'","'.$c3.'","'.$c4.'","'.$c5.'","'.$c6.'" ';
                                
                $sOutput .= '],';

            }
            $sOutput = substr_replace( $sOutput, "", -1 );
            $sOutput .= '] }';
        }else{
            $sOutput = $rResult['error'];
        }
        
        echo $sOutput;

    }    
    
    public function getGridTiempoOrden(){
        return Obj::run()->seguimientoPagoController->getGridTiempoOrden();
    }
}

?>