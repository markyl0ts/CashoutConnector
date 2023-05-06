<?php 
$data = array();

if(isset($_GET['action']))
{
    include('includes/functions.php');
    $action = $_GET['action'];

    if($action == "bycontact")
    {
        $contactId = $_GET['id'];
        try {
            $conn = OpenConnection();
            $sqlQry = "SELECT * FROM [Wallet] WHERE [ContactId] = ".$contactId;
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
            $balance = $_GET['balance'];
            $id = $_GET['contactId'];
            try {
                $conn = OpenConnection();
                if (sqlsrv_begin_transaction($conn) == FALSE){
                    $dbFlag = 2;
                }
        
                $sqlQry = "UPDATE [Wallet] SET [Balance] = [Balance] - $balance WHERE ContactId = $id";
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