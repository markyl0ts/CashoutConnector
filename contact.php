<?php 
$data = array();

if(isset($_GET['action']))
{
    include('includes/functions.php');
    $action = $_GET['action'];

    if($action == "byphone")
    {
        $phone = $_GET['phone'];
        try {
            $conn = OpenConnection();
            $sqlQry = "SELECT * FROM [Contact] WHERE [PhoneNo] ='". $phone ."'";
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
}

header("Content-Type: application/json");
echo json_encode($data, true);
exit();