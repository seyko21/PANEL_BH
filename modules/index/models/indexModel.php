<?php

/*
 * Documento   : indexModel
 * Creado      : 30-ene-2014, 17:38:01
 * Autor       : RDCC
 * Descripcion :
 */

class indexModel extends Model {

    public $_usuario;
    public $_idPersona;
    
    public function __construct() {
        parent::__construct();
        $this->_usuario = Session::get("sys_idUsuario");
        $this->_idPersona = Session::get("sys_idPersona");
    }

    public function getFoto(){
        $query = "SELECT 
                    foto
                FROM mae_usuario WHERE id_usuario = :idUsuario;";
        
        $parms = array(
            ':idUsuario'=>$this->_usuario
        );
        
        $data = $this->queryOne($query,$parms);
        return $data;
    }
    
    public function postFoto($doc){
        $query = "UPDATE `mae_usuario` SET
                    `foto` = :foto
                WHERE `id_usuario` = :idUsuario;";
        $parms = array(
            ':idUsuario' => $this->_usuario,
            ':foto' => $doc
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }
    
    public function anulaCotizacionesVencidas(){
        $query = "CALL sp_cotiAnularCotizacion(:flag,:criterio);";
        $parms = array(
            ':flag' => 1,
            ':criterio' => ''
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }
    
    public function generarUtilidadSocio(){
        $query = "CALL sp_ordseCalcularUtilidadSocio(:flag,:criterio);";
        $parms = array(
            ':flag' => 1,
            ':criterio' => $this->_usuario
        );
        $this->execute($query,$parms);
        $data = array('result'=>1);
        return $data;
    }
    
    public function getIndexGraficoIngreso(){
        $query = "SELECT
                SUM(p.`monto_pago`)AS monto,
                MONTH(p.`fecha_pago`) AS mes
        FROM lgk_pago p
        WHERE p.estado = :estado AND LEFT(p.`fecha_pago`,4) = YEAR(CURDATE())
        GROUP BY 2;";
        $parms = array(
            ':estado' => 'P'
        );
        $data = $this->queryAll($query,$parms);
        return $data;
    }

    public function getIndexGraficoIngresoSocio(){
        $query = "SELECT
                    sum( tb.`monto_neto`) as monto, 
                    MONTH( tb.`fecha_creacion`)	as mes             
             FROM lgk_comisionvendedor cs 
                    inner join `tes_boleta` tb on tb.`id_comision` = cs.`id_comision`
             WHERE cs.`origen` = :origen AND tb.`estado` <> :estado and cs.`id_persona` = :idSocio;";
        $parms = array(
            ':origen' => 'S',
            ':estado' => 'A',
            ':idSocio' => $this->_idPersona
        );
        $data = $this->queryAll($query,$parms);
        
        return $data;
    }
    
    
}

?>
