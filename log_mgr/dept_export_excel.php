<?php
header( "Content-type: application/vnd.ms-excel; charset=utf-8" );
header( "Expires: 0" );
header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
header( "Pragma: public" );
header( "Content-Disposition: attachment; filename=department_log.xls" );
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        
<?php
            
            require_once '../setting.php';
            $conn = mssql_connect($db_host, $db_user, $db_pw);
            mssql_select_db($db_name, $conn);
            $sql = "";
            if(isset($_POST['keyword'])){
                $p_name = $_POST['keyword'];
              
                $sql = $sql."select dept_id, log_name, log_date, dept_name from log_Department where log_name = '%{$p_name}%' or log_date = '%{$p_name}%' or dept_name = '%{$p_name}%'";
            }
            else{
                $sql = $sql."select dept_id, log_name, log_date, dept_name from log_Department";
            }
            $result = mssql_query($sql,$conn);
            echo "<table border='1'><tr>";
            for($i = 1; $i < mssql_num_fields($result); $i++) {
            $field_info = mssql_fetch_field($result, $i);
            echo "<th>{$field_info->name}</th>";
        }
        echo "</tr>";

// Print the data
    while($row = mssql_fetch_row($result)) {
        $num = 0;
        $arraypass[5];
        echo "<tr>";
        foreach($row as $_column) {
            if($num==0){
               
            }
            else{
                echo '<td ><input type ="text" value = "'.$_column.'" disabled = true ></td>';
            }
            $num = $num+1;
        }
       
        
        echo "</tr>";
    }

echo "</table>";
 ?>
        
            
        </form>
    </body>
</html>
