<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/master.css?v=1.01">
    <title>Calendar</title>
  </head>
<?php
//Author: Johnathon Southworth
//Class: CS296 PHP Jeff Miller
//Lab: 3 -- calendar
require_once('includes/dbconn.php');
$dt = isset($_GET['dt']) ? $_GET['dt'] : '';
session_start();

if (isset($_SESSION['uid'])) {
  $uid = $_SESSION['uid'];
}

try {
    $somedate = new DateTime($dt);
} catch (Exception $e) {
    $somedate = new DateTime;
}

$year = $somedate->format('Y');
$month = $somedate->format('m');
$today = $somedate->format('d');

if (!empty($dt)) {
  $connect = new Connection;
  $connection = $connect->getConnection();
  $sql = $connection->prepare('CALL sp_getActive(?, ?, ?)');
  $sql->execute(array($year, $month, $today));
  $results = $sql->fetch();

  if (empty($results['title'])) {
    $results['title'] = 'No event information available';
  }
  if (empty($results['description'])) {
    $results['description'] = 'Not set';
  }

if (empty($results['location'])) {
  $results['location'] = 'Not set';
}
if (empty($results['url'])) {
  $results['url'] = 'Not Set';
}

    echo "<div class=\"eventInfo\"><h1>Today's active events</h1>
    <ul>
    <h3><li>Title: {$results['title']}</h3></li>
          <li><h3>Description: {$results['description']}</h3></li>
          <li><h3>Location: {$results['location']}</h3></li>
          <li><h3>URL: {$results['url']}</h3></li>
          </ul>
          </div>
          <br /> <br /> <br /> <br /><br /> <br /> <br /><br /> <br /> <br /><br /> <br /> <br /><br /> <br />";
}

$selfURL = $_SERVER['SCRIPT_NAME'];
$formattedMonth = $somedate->format('F');
$first = new DateTime;
$first->setDate($year, $month, 1); //get first day of month
$namedFirstDay = $first->format('l');

$prevMonth = clone $somedate;
$prevMonth = $prevMonth->sub(new DateInterval('P1M'));
$nextMonth = clone $somedate;
$nextMonth = $nextMonth->add(new DateInterval('P1M'));
$last = new DateTime;
$last->setDate($year, $month + 1, 1);
$last->sub(new DateInterval('P1D')); //subtract one from first day of 'next' month
$daysInMonth = $last->format('d'); //last day of month
$namedDays = array(
    'Sunday',
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday'
);
?>

  <body>
    <div class="month-heading">
      <a href="<?="$selfURL?dt={$prevMonth->format('Y-m-d')}"?>">Prev</a>
      <h3><?=$formattedMonth . ' ' . $year;?></h3>
      <a href="<?="$selfURL?dt={$nextMonth->format('Y-m-d')}"?>">Next</a>
    </div>
<div>
    <table class="calendar">
        <thead>
            <tr>
<?php
//loop to create calendar heading "sunday-monday"
foreach($namedDays as $days) {
    echo "<th> $days </th>";
}
?>
            </tr>

        </thead>
        <tbody>
 <?php
 $connect = new Connection;
 $connection = $connect->getConnection();
 $sql = $connection->prepare('CALL sp_getEvents(?, ?)');
 $sql->execute(array($month, $year));
 $results = $sql->fetchAll();
 // echo "<pre>".print_r($results, true)."</pre>";
 $eventResults = array();
 for ($i = 0; $i < count($results); $i++) {
   $date = new DateTime($results[$i]{'eventTime'});
   $eventKey = intval($date->format('d'));
   if (!isset($eventResults[$eventKey])) {
         $eventResults[$eventKey] = array();
   }
   $eventResults[$eventKey][] = array(
     "title"=>$results[$i]{'title'},
     "location"=>$results[$i]{'location'},
     "time"=>$date->format('H:i')
   );
 }
        $dayCount = 0;
        $started = false;
        while ($dayCount <= $daysInMonth) {
            echo "<tr>";
            for ($i = 0; $i < 7; $i++) {
                $day = $namedDays[$i];
                if($day === $namedFirstDay) {
                    $started = true;
                }
                echo "<td>";
                if ($started) {
                  $events = array();
                  $events[] =+ $dayCount;
                     ++$dayCount;
                     if ($dayCount <= $daysInMonth) {
                         echo "<a href =\"{$selfURL}?dt=$year-$month-$dayCount\">{$dayCount}</a>";
                         if (isset($eventResults[$dayCount])) {
                           foreach($eventResults[$dayCount] as $info) {
                             echo "<br>". "<strong>" . $info{'time'} . "</strong>" . ' ' . $info{'title'};
                           }
                         }
                     }
                }
                echo "</td>";
            }
            echo "</tr>";

        }
        // print_r($events);
            ?>
        </tbody>

    </table>
    <a href="admin.php">Admin</a>
    <?php
      if (!empty($_SESSION['uid'])) {
        echo "<a href=\"logout.php\">Logout?</a>";
      }
     ?>
  </div>
  <script type="text/javascript">

  </script>
  </body>
</html>
