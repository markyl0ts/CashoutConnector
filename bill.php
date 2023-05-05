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
}

header("Content-Type: application/json");
echo json_encode($data, true);
exit();