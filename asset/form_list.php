<?php

session_cache_limiter('nocache, must-revalidate');
    session_start();
    echo "account : ". $_SESSION['user_id'];
    
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    
        
<?php
               
            include_once("../header.php");
            include_once ("../form_search.html");
?>
   
        <?php 
        
        if($_SESSION['user_id']=='admin'){
        ?>
        <form method = 'post' action = 'form_list.php'>
            <input type ='submit' name ='ssuubmit' value ='check'>
            <input type ='hidden' name ='check' value =1>
        </form>
<?php
        }   
        ?>
    
        <form method ='post' action="../do_export_excel.php">
            <input type ='hidden' name ='searchtext' value ='<?php echo $_POST['keyword'];?>'>
            <input type ='hidden' name ='checkvalue' value =<?php echo $_POST['check'];?>>
            <input type ='submit' name ='print' value = 'excel'>
        </form>

        <?php  
            $delay_day = 3;
            $delay_to_check = date('Y-m-d',time()-(60*60*24*($delay_day)));
            $db_host = "localhost";
            $db_user = "sa";
            $db_pw = "vamosit";
            $db_name = "senacyt_asset";
            $conn = mssql_connect($db_host, $db_user, $db_pw);
            mssql_select_db($db_name, $conn);
            $sql = "";
            $check = $_POST['check'];
            if(isset($_POST['keyword'])){
                $p_name = $_POST['keyword'];
                
                $sql = $sql."select asset_id, asset_barcode barcode, 
                    t_name tipo,
                asset_desc _description,
                asset_brand brand,
                asset_model model,
                asset_serial serialnumber,
                asset_details details,
                asset_bought_date purchase_date,
                asset_last_touch last_handled,
                asset_guarantee_expired guarantee_expired,
                asset_out out_date,
                asset_in in_date,
                asset_price price,
                p_id,
                asset_provider _provider,
		p_name person, dept_name department,
                loc_building as building, 
                loc_floor as _floor, 
                loc_desc location_description,
                pos as available
		from Asset A inner join tipo T on T.t_id = A.t_id inner join Person P on A.p_id = P.p_id inner join Loc L on L.loc_id= A.loc_id inner join Department D on D.dept_id = P.dept_id where L.loc_building like '%".$p_name."%' or  asset_brand  like '%".$p_name."%' or asset_desc like '%".$p_name."%'"
                        . " or asset_barcode like '%".$p_name."%'";
            }
            else if(isset($_POST['tipo'])&&$_POST['tipo']!='basicoption'){
                
                $p_name = $_POST['tipo'];

                $sql = $sql."select asset_id, 
                t_name tipo,
                asset_barcode barcode, 
                asset_desc _description,
                asset_brand brand,
                asset_model model,
                asset_serial serialnumber,
                asset_details details,
                asset_bought_date purchase_date,
                asset_last_touch last_handled,
                asset_guarantee_expired guarantee_expired,
                asset_out out_date,
                asset_in in_date,
                asset_price price,
          A.p_id as p_id,
                asset_provider _provider,
		p_name person, dept_name department,
                loc_building as building, loc_floor as _floor, loc_desc location_description,
                pos as available
                from Asset A inner join tipo T on T.t_id = A.t_id inner join Person P on A.p_id = P.p_id inner join Loc L on L.loc_id= A.loc_id inner join Department D on D.dept_id = P.dept_id where t_name='{$p_name}';";
                
            }
            else if($check==1){
                $sql = $sql."select asset_id, 
                    t_name tipo,
                asset_barcode barcode, 
                asset_desc _description,
                asset_brand brand,
                asset_model model,
                asset_serial serialnumber,
                asset_details details,
                asset_bought_date purchase_date,
                asset_last_touch last_handled,
                asset_guarantee_expired guarantee_expired,
                asset_out out_date,
                asset_in in_date,
                asset_price price,
           A.p_id as p_id,
                asset_provider _provider,
		p_name person, dept_name department,
                loc_building as building, loc_floor as _floor, loc_desc location_description,
                pos as available
		from Asset A inner join tipo T on T.t_id = A.t_id inner join Person P on A.p_id = P.p_id inner join Loc L on L.loc_id= A.loc_id inner join Department D on D.dept_id = P.dept_id where asset_last_touch <= '".$delay_to_check."'";
            }
  
            else{
                
                $sql = $sql."select asset_id, 
t_name tipo,                    
asset_barcode barcode, 
                    
asset_desc _description,
 asset_brand brand,
  asset_model model,
   asset_serial serialnumber,
                asset_details details,
                asset_bought_date purchase_date,
                asset_last_touch last_handled,
                asset_guarantee_expired guarantee_expired,
                asset_out out_date,
                asset_in in_date,
                asset_price price,
               A.p_id as p_id,
	    asset_provider _provider,
		 p_name person, dept_name department,
	    loc_building as building, loc_floor as _floor, loc_desc location_description,
            pos as available
		 from Asset A inner join tipo T on T.t_id = A.t_id inner join Person P on A.p_id = P.p_id inner join Loc L on L.loc_id= A.loc_id inner join Department D on D.dept_id = P.dept_id ;";
            }
            $result = mssql_query($sql,$conn);
            $sql_tipo = "select * from dbo.tipo;";
            $result_tipo = mssql_query($sql_tipo, $conn);
            ?>
        <br>
        
       Buscar tipos : <form method='post' action='form_list.php'>
            <select name='tipo'>
                <option value='basicoption'> Todo </option>
            <?php
            while($row_tipo = mssql_fetch_array($result_tipo)){
                
                ?>
                <option value="<?php echo $row_tipo['t_name'];?>"> <?php echo $row_tipo['t_name']; ?> </option>
                
                <?php
            }
            ?>
            </select>    
          
            <input type='submit' name='sub' value='buscar'>
            
        </form>

                
        <table class="marginleft">
           <tr class="tablecolor">
            <th>Placa</th>
            <th>Tipo </th>
            <th>Descripción</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Serial</th>
            <th>Detalles</th>
            <th>Día de compra</th>
            <th>Final de garantía </th>
            <th>Salir activo</th>
            <th>Entrar activo</th>
            <th>Precio de compra</th>
            <th>Proveedor</th>
            <th>Funcionario</th>
            <th>Departamento</th>
            <th>Ubicación</th>
            <th>Posible </th>
            
           
            <?php
            if($_SESSION['user_id']=='admin'){
            ?>
         
            <th> Admin </th>
            <?php }?>
            
            
            
        
                
                
                <?php
$unhandled = 1; //define checking period;
while($row = mssql_fetch_array($result)) {
?>
	</tr>
            <tr>
                <td id="centro"><?php echo $row['barcode'];?></td>
                <td id="centro"><?php echo $row['tipo'];?></td>
                <td id="centro"><?php echo $row['_description'];?></td>
                <td id="centro"><?php echo $row['brand'];?></td>
                <td id="centro"><?php echo $row['model'];?></td>
                <td id="centro"><?php echo $row['serialnumber'];?></td>
                <td id="centro"><?php echo $row['details'];?></td>
                <td id="centro"><?php echo date('d-m-Y',strtotime($row['purchase_date']));?></td>
                <td id="centro"><?php echo date('d-m-Y',strtotime($row['guarantee_expired']));?></td>
                <td id="centro"><?php echo date('d-m-Y',strtotime($row['out_date']));?></td>
                <td id="centro"
                    <?php
                     $strlast = $row['in_date'];
                $end = new DateTime($strlast);
                $start = new DateTime();
                $interval = (int) ($start->format('U')-$end->format('U'))/(60*60*24);
                if($interval>$unhandled&&$row['available']==0){ 
                    echo "bgcolor='#FF0000'";
                } 
                    
                    ?>
                    ><?php echo date('d-m-Y',strtotime($row['in_date']));?></td>
                <td id="centro"><?php echo $row['price'];?></td>
                <td id="centro"><?php echo $row['_provider'];?></td>
                <td id="centro"><?php echo $row['person'];?></td>
                <td id="centro"><?php echo $row['department'];?></td>
                <td id="centro"><?php echo $row['building']." ".$row['_floor']." ".$row['location_description'];?></td>
                <td id="centro"><?php 
                if($row['available']==1){
                    echo "O";
                }
                else{
                    echo "X";
                }
                 if($_SESSION['user_id']=='admin'){

                ?>
                <td>
                    <form method = 'post'>
                        <input type ='hidden' name ='asset_id' value ='<?php echo $row['asset_id'];?>'>
                        <input type ='hidden' name ='p_id' value ='<?php echo $row['p_id'];?>'>
                        <?php if($row['available']==1){?>
                        <input type ='submit' value ='alquilar' formaction="form_rent.php">
                        <input type ='submit' value ='asignar' formaction="form_assign.php">
                        <?php }?>
                        <input type ='submit' value ='modificar' formaction="form_modify.php">
                        <?php
                        if($row['available']==0){
                            ?>  
                               <!-- <input type ='submit' value ='transferir' formaction="form_rent.php"> -->
                                <input type ='submit' value ='devolver' formaction="do_return.php">
                            <?php
                        }
                        ?>
                       <input type ="submit" value ='borrar' formaction='do_delete.php'>
                        
                        
                    </form>
                    
                </td>
                <?php
                
                        } ?>    
                
            </tr> 
<?php }?>
            </table>
        
        <?php include_once '../footer.php';?>
    </body>
</html>
