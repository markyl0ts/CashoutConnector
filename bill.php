<?php 
$data = array();

if(isset($_GET['action']))
{
    include('includes/functions.php');
    $action = $_GET['action'];

    if($action == "bymachine")
    {
        $id = $_GET['id'];
        try {
            $conn = OpenConnection();
            $sqlQry = "SELECT * FROM [BillCounter] WHERE SystemId = $id";
            $getRecord = sqlsrv_query($conn, $sqlQry);
            if ($getRecord == FALSE)
                die(FormatErrors(sqlsrv_errors()));
    
            while($row = sqlsrv_fetch_array($getRecord, SQLSRV_FETCH_ASSOC))
            {
                $data = array(
                    "b50" => $row['50Bill'],
                    "b100" => $row['100Bill'],
                    "b200" => $row['200Bill'],
                    "b500" => $row['500Bill'],
                    "b1000" => $row['1000Bill']
                );
            }
            
            sqlsrv_free_stmt($getRecord);
            sqlsrv_close($conn);
        } catch(Exception $e){}
    }

    if($action == "update")
    {
        $systemId = $_GET['systemId'];
        $bc50 = $_GET['bc50'];
        $bc100 = $_GET['bc100'];
        $bc200 = $_GET['bc200'];
        $bc500 = $_GET['bc500'];
        $bc1000 = $_GET['bc1000'];

        try {
            $conn = OpenConnection();
            if (sqlsrv_begin_transaction($conn) == FALSE){
                $dbFlag = 2;
            }
    
            $sqlQry = "UPDATE [BillCounter] SET 
                            [50Bill] = [50Bill] - ".$bc50.", 
                            [100Bill] = [100Bill] - ".$bc100.", 
                            [200Bill] = [200Bill] - ".$bc200.", 
                            [500Bill] = [500Bill] - ".$bc500.", 
                            [1000Bill] = [1000Bill] - ".$bc1000." 
                        WHERE SystemId = ". $systemId;
            $exec = sqlsrv_query($conn, $sqlQry);
            
            if($exec){
                sqlsrv_commit($conn);
                $data = array("trans" => 1);
            } else 
                $data = array("trans" => 0);
            
            sqlsrv_free_stmt($exec);
            sqlsrv_close($conn);
        } catch(Exception $e){}
    }
}

header("Content-Type: application/json");
echo json_encode($data, true);
exit();