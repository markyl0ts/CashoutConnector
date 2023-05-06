<?php 
$data = array();

if(isset($_GET['action']))
{
    include('includes/functions.php');
    $action = $_GET['action'];

    if($action == "get")
    {
        $id = $_GET['id'];
        $guid = "";
        $name = "";
        $balance = 0;
        $rateId = 0;
        $accuAmount = 0;

        try {
            $conn = OpenConnection();
            $sqlQry = "SELECT * FROM [System] WHERE Id = ". $id;
            $getRecord = sqlsrv_query($conn, $sqlQry);
            if ($getRecord == FALSE)
                die(FormatErrors(sqlsrv_errors()));
  
            while($row = sqlsrv_fetch_array($getRecord, SQLSRV_FETCH_ASSOC))
            {
                $data = $row;
            }
            
            sqlsrv_free_stmt($getRecord);
            sqlsrv_close($conn);
        } catch(Exception $e){}
    }

    if($action == "update")
    {
        $field = $_GET['field'];
        if($field == "balance")
        {
            $id = $_GET['id'];
            $total = $_GET['total'];
            try {
                $conn = OpenConnection();
                if (sqlsrv_begin_transaction($conn) == FALSE){
                    $dbFlag = 2;
                }
        
                $sqlQry = "UPDATE [System] SET [Balance] = ".$total." WHERE Id = $id";
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

        if($field == "AccuAmount")
        {
            $id = $_GET['id'];
            $fee = $_GET['fee'];
            try {
                $conn = OpenConnection();
                if (sqlsrv_begin_transaction($conn) == FALSE){
                    $dbFlag = 2;
                }
        
                $sqlQry = "UPDATE [System] SET [AccumulatedAmount] = [AccumulatedAmount] + $fee WHERE Id = $id";
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
}

header("Content-Type: application/json");
echo json_encode($data, true);
exit();

?>