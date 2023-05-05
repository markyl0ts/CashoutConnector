<?php 
$data = array();

if(isset($_GET['action']))
{
    include('includes/functions.php');
    $action = $_GET['action'];

    if($action == "add")
    {
        $systemId = $_GET['systemId'];
        $rateRangeId = $_GET['rateRange'];
        $amount = $_GET['amount'];
        $contactId = $_GET['contactId'];
        $reference = $contactId.rand(100000000,999999999);
        $insId = 0;

        try {
            $conn = OpenConnection();
            $sqlQry = "
                        INSERT INTO [Transaction]([SystemId],[ContactId],[RateRangeId],[reference],[amount],[status]) OUTPUT INSERTED.Id
                        VALUES($systemId,$contactId,$rateRangeId,'".$reference."',$amount,0);
                    ";

            $getRecord = sqlsrv_query($conn, $sqlQry);
            if ($getRecord == FALSE)
                die(print_r(sqlsrv_errors()));

            while($row = sqlsrv_fetch_array($getRecord, SQLSRV_FETCH_ASSOC))
            {
                $data = $row;
            }
            
            sqlsrv_free_stmt($getRecord);
            sqlsrv_close($conn);
      } catch(Exception $e){}
    }

    if($action == "get"){
        $id = $_GET['id'];
        try {
            $conn = OpenConnection();
            $sqlQry = "SELECT * FROM [Transaction] WHERE Id = $id";

            $getRecord = sqlsrv_query($conn, $sqlQry);
            if ($getRecord == FALSE)
                die(print_r(sqlsrv_errors()));

            while($row = sqlsrv_fetch_array($getRecord, SQLSRV_FETCH_ASSOC))
            {
                $data = $row;
            }
            
            sqlsrv_free_stmt($getRecord);
            sqlsrv_close($conn);
      } catch(Exception $e){}
    }

    if($action == "reference")
    {
        $reference = $_GET['reference'];

        try {
            $conn = OpenConnection();
            $sqlQry = "SELECT * FROM [Transaction] WHERE [Reference] = '". $_GET['reference']."'";
            $getRecord = sqlsrv_query($conn, $sqlQry);
            if ($getRecord == FALSE)
                die(FormatErrors(sqlsrv_errors()));

            while($row = sqlsrv_fetch_array($getRecord, SQLSRV_FETCH_ASSOC))
            {
                $data = $row;
            }
            
            sqlsrv_free_stmt($getRecord);
            sqlsrv_close($conn);
        } catch(Exception $e){
            
        }
    }

    if($action == "update")
    {
        $subAct = $_GET['sb'];
        if($subAct == "status")
        {
            $status = $_GET['status'];
            $reference = $_GET['reference'];
            
            try {
                $conn = OpenConnection();
                if (sqlsrv_begin_transaction($conn) == FALSE){
                    die(sqlsrv_errors());
                }
        
                $sqlQry = "UPDATE [Transaction] SET [Status] = ".$status." WHERE [Reference] = '".$reference."'";
                $exec = sqlsrv_query($conn, $sqlQry);
        
                if($exec){
                    sqlsrv_commit($conn);
                    $data = array("success" => 1);
                }
                
                sqlsrv_free_stmt($exec);
                sqlsrv_close($conn);
            } catch(Exception $e){}
        }
    }
}

header("Content-Type: application/json");
echo json_encode($data, true);
exit();