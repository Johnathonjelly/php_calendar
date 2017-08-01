<?php
require_once('includes/dbconn.php');
session_start();

if (isset($_SESSION['uid'])) {
  $uid = $_SESSION['uid'];
} else {
  header('Location: login.php');
}

isset($_GET['eventID']) ? $eventID = $_GET['eventID'] : $eventID = '';
$connect = new Connection;
$connection = $connect->getConnection();
$sql = $connection->prepare('CALL sp_deleteEvent(?)');
$sql->execute(array($eventID));
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Delete Event</title>
  </head>
  <body>
    <script>
    window.setTimeout( () => {
        window.location = 'admin.php';
      }, 1500);
    </script>
    <div>
      <?php
        echo "{$eventID} deleted sucessfully."
      ?>
    </div>

  </body>
</html>
