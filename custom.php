<?php 
$data = array();

if(isset($_GET['action']))
{
    include('includes/functions.php');
    $action = $_GET['action'];

    if($action == "print_data")
    {
        $reference = $_GET['reference'];
        try {
            $conn = OpenConnection();
            $sqlQry = "SELECT 
                            t.*,
                            c.FullName,
                            c.PhoneNo,
                            (SELECT w.Balance FROM [Wallet] w WHERE w.ContactId = t.ContactId) as 'Balance',
                            (SELECT rr.Fee FROM RateRange rr WHERE rr.Id = t.RateRangeId) as 'Fee'
                        FROM [Transaction] t JOIN [Contact] c 
                            ON t.ContactId = c.Id
                    WHERE [Reference] = '". $_GET['reference']."'";
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