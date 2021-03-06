<?php
/*
* ---------------------------------------
* --------- CREATED BY CREATOR ----------
* fecha: 18-09-2014 22:09:09 
* Descripcion : instalacionModel.php
* ---------------------------------------
*/ 

class instalacionModel extends Model{

    private $_flag;
    private $_idInstalacion;
    private $_xSearch;
    private $_usuario;
    private $_chkdel;
    public  $_cod;
    private $_fecha;
    private $_idOrdenDetalle;
    private $_montoTotal;
    private $_observacion;
    private $_idConcepto;
    private $_cantidad;
    private $_precio;
    private $_tipo;

    /*para el grid*/
    public  $_iDisplayStart;
    private $_iDisplayLength;
    private $_iSortingCols;
    private $_sSearch;
    
    public function __construct() {
        parent::__construct();
        $this->_set();
    }
    
    private function _set(){
        $this->_flag        = Formulario::getParam("_flag");
        $this->_idInstalacion   = Aes::de(Formulario::getParam("_idInstalacion"));    /*se decifra*/
        $this->_usuario     = Session::get("sys_idUsuario");
        $this->_xSearch     = Formulario::getParam(ORINS."_term");
        $this->_chkdel  = $this->post(ORINS.'chk_delete');
        $this->_fecha     = Functions::cambiaf_a_mysql(Formulario::getParam(ORINS."txt_fechains"));
        $this->_idOrdenDetalle     = AesCtr::de(Formulario::getParam(ORINS."txt_idcaratula"));
        $this->_montoTotal     = Functions::deleteComa(Formulario::getParam(ORINS."txt_total"));
        $this->_observacion     = Formulario::getParam(ORINS."txt_obs");
        $this->_idConcepto     = Formulario::getParam(ORINS."hhddIdConcepto"); #array
        $this->_cantidad     = Formulario::getParam(ORINS."txt_cantidad");#array
        $this->_precio     = Formulario::getParam(ORINS."txt_precio");#array
        $this->_tipo     = Formulario::getParam("_tipo");
        $this->_cod     = Formulario::getParam("_cod");
        
        $this->_iDisplayStart  = Formulario::getParam("iDisplayStart"); 
        $this->_iDisplayLength = Formulario::getParam("iDisplayLength"); 
        $this->_iSortingCols   = Formulario::getParam("iSortingCols");
        $this->_sSearch        = Formulario::getParam("sSearch");
    }
    
    /*data para el grid: Instalacion*/
    public function getInstalaciones(){
        $aColumns       =   array("","orden_numero","ordenin_numero","codigo","ubicacion","fecha_instalacion","monto_total" ); //para la ordenacion y pintado en html
        /*
	 * Ordenando, se verifica por que columna se ordenara
	 */
        $sOrder = "";
        for ( $i=0 ; $i<intval( $this->_iSortingCols ) ; $i++ ){
                if ( $this->post( "bSortable_".intval($this->post("iSortCol_".$i)) ) == "true" ){
                        $sOrder .= " ".$aColumns[ intval( $this->post("iSortCol_".$i) ) ]." ".
                                ($this->post("sSortDir_".$i)==="asc" ? "asc" : "desc") .",";
                }
        }
         $sOrder = substr_replace( $sOrder, "", -1 );
        $query = "call sp_ordseIinstalacionGrid(:acceso,:usuario,:iDisplayStart,:iDisplayLength,:sOrder,:sSearch);";
        
        $parms = array(
            ":acceso" => Session::get('sys_all'),
            ":usuario" => $this->_usuario,
            ":iDisplayStart" => $this->_iDisplayStart,
            ":iDisplayLength" => $this->_iDisplayLength,
            ":sOrder" => $sOrder,
            ":sSearch" => $this->_sSearch,
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    public function getCaratulas(){
        /*bueca en las ordenes en estado T o P, pagado parcial o total y flag_instalacion = 0*/
        $u = '';
        if(!empty($this->_xSearch)){
            $u = " AND o.`orden_numero` like '%".$this->_xSearch."%' ";
        }
        $query = "
         SELECT 
                od.`id_ordenserviciod`,
                c.`codigo`,
                c.`descripcion`,
                ct.`ubicacion`,
                o.`orden_numero`
        FROM `lgk_ordenserviciod` od
        INNER JOIN `lgk_caratula` c ON c.`id_caratula`=od.`id_caratula`
        INNER JOIN `lgk_catalogo` ct ON ct.`id_producto`=c.`id_producto`
        INNER JOIN `lgk_ordenservicio` o ON o.`id_ordenservicio`=od.`id_ordenservicio`
        WHERE (o.`estado`='T' OR o.`estado`='P') AND od.flag_instalacion = '0' ".$u." ; ";
        
        $parms = array();
        $data = $this->queryAll($query,$parms);
        return $data;
    }
    
    public function getConceptos(){
        if($this->_tipo == 'P'){ # produccion
            $destino = 'P';
        }else if($this->_tipo == 'I'){ # instalacion
            $destino = 'S';
        }
        $query = "
        SELECT 
                c.`id_concepto`,
                c.`descripcion`,
                c.`precio`,
                tc.`descripcion` AS tconcepto
        FROM `pub_concepto` c 
        INNER JOIN `pub_tipoconcepto` tc ON tc.`id_tipo`=c.`id_tipo`
        WHERE c.`estado` = :estado AND c.`destino` in ('G',:destino)
        ORDER BY 4,2; ";
        
        $parms = array(
            ':estado'=>'A',
            ':destino'=> $destino 
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }

    /*grabar nuevo registro: Instalacion*/
    public function newInstalacion(){
        $query = "CALL sp_ordseInstalacionMantenimiento("
                . ":flag,"
                . ":idInstalacion,"
                . ":fecha,"
                . ":idOrdenDetalle,"
                . ":montoTotal,"
                . ":obs,"
                . ":idConcepto,"
                . ":cantidad,"
                . ":precio,"
                . ":usuario"
            . "); ";
        
        $parms = array(
            ':flag'=> 1,
            ':idInstalacion'=>'',
            ':fecha'=> $this->_fecha,
            ':idOrdenDetalle'=> $this->_idOrdenDetalle,
            ':montoTotal'=> $this->_montoTotal,
            ':obs'=> $this->_observacion,
            ':idConcepto'=> '',
            ':cantidad'=> '',
            ':precio'=> '',
            ':usuario'=> $this->_usuario
        );
        $data = $this->queryOne($query,$parms);
        
        $idInstalacion = $data['idInstalacion'];
        
        if($data['result'] == '1'){
            /*detalle de instalacion*/
            foreach ($this->_idConcepto as $key=>$idConcepto) {
                $parmsx = array(
                    ':flag'=> 2,
                    ':idInstalacion'=>$idInstalacion,
                    ':fecha'=> $this->_fecha,
                    ':idOrdenDetalle'=> '',
                    ':montoTotal'=> '',
                    ':obs'=> '',
                    ':idConcepto'=> AesCtr::de($idConcepto),
                    ':cantidad'=> Functions::deleteComa($this->_cantidad[$key]),
                    ':precio'=> Functions::deleteComa($this->_precio[$key]),
                    ':usuario'=> $this->_usuario
                );
                $this->execute($query,$parmsx);
            }
        }
        $datax = array('result'=>1);
        return $datax;
    }
    
    public function findInstalacionDetalle(){
       $query = "
        SELECT 
                od.`id_concepto`,
                c.`descripcion` AS concepto,
                od.`cantidad`,
                od.`costo_importe` AS precio,
                od.`costo_total`
        FROM `lgk_ordeninstalaciond`  od
        INNER JOIN `pub_concepto` c ON c.`id_concepto`=od.`id_concepto`
        WHERE od.`id_ordeninstalacion` = :idInstalacion; ";
        
        $parms = array(
            ':idInstalacion'=>  $this->_idInstalacion
        );
        $data = $this->queryAll($query,$parms);
        return $data; 
    }
    
    public function getOrdenInstalacion(){
        $query = "CALL sp_ordseIinstalacionConsultas(:flag,:idInstalacion);";
        
        $parms = array(
            ':flag'=>  1,
            ':idInstalacion'=>  $this->_idInstalacion
        );
        $data = $this->queryAll($query,$parms);
        return $data; 
    }
    
    public function anularInstalacionAll(){
        $query = "CALL sp_ordseInstalacionMantenimiento("
                . ":flag,"
                . ":idInstalacion,"
                . ":fecha,"
                . ":idOrdenDetalle,"
                . ":montoTotal,"
                . ":obs,"
                . ":idConcepto,"
                . ":cantidad,"
                . ":precio,"
                . ":usuario"
            . "); ";
        
        foreach ($this->_chkdel as $value) {
            $parms = array(
                ':flag'=> 3,
                ':idInstalacion'=>Aes::de($value),
                ':fecha'=> '',
                ':idOrdenDetalle'=> '',
                ':montoTotal'=> '',
                ':obs'=> '',
                ':idConcepto'=> '',
                ':cantidad'=> '',
                ':precio'=> '',
                ':usuario'=> $this->_usuario
            );
            $this->execute($query,$parms);
        }
        $data = array('result'=>1);
        return $data;
    }
    
}

?>