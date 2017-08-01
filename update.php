<?php
//Author: Johnathon Southworth
//Class: CS296 PHP Jeff Miller
//Lab: 3 -- calendar
//this page is basically the exact same as admin.php with minor changes. Notable -- different stored procedure used to update and getting event ID from query string
require_once('includes/dbconn.php');
session_start();
$msgs = array();
$eventID = isset($_POST['didSubmit']) ? $_POST['didSubmit'] :  '';
if (empty($eventID)) {
  $eventID = isset($_GET['eventID']) ? $_GET['eventID'] : '';
}

if (isset($_SESSION['uid'])) {
  $uid = $_SESSION['uid'];
} else {
  header('Location: login.php');
}

//connect and get info to populate form for update
$connect = new Connection;
$connection = $connect->getConnection();
$sql = $connection->prepare('CALL sp_getEventForUpdate(?)');
$sql->execute(array($eventID));
$results = $sql->fetchAll();
if (count($results) == 0 ) {
  header('Location: admin.php?msg=invalid+event+ID');
}

//when submit button is pressed do the update
if (isset($_POST['didSubmit'])) {
  $eventTitle = isset($_POST['title']) ? $_POST['title'] : '';
  $eventDescription = isset($_POST['description']) ? $_POST['description'] : '';
  isset($_POST['timeAndDate']) ? $_POST['timeAndDate'] : '';
  $eventActive = isset($_POST['active']) ? $_POST['active'] : '';
  $eventURL =  isset($_POST['url']) ? $_POST['url'] : '';
  $eventLocation = isset($_POST['location']) ? $_POST['location'] : '';
  $dateTimes = isset($_POST['timeAndDate']) ? $_POST['timeAndDate'] : array();

//if * empty give correct messages
    if (empty($_POST['title'])) {
      $msgs[] = "The event must have a title. ";
    }
    if (empty($_POST['description'])) {
      $msgs[] = "The event must have a description. ";
    }
    $sql = $connection->prepare('CALL sp_updateEvent(?, ?, ?, ?, ?, ?)');
    $sql->execute(array($eventTitle, $eventDescription, $eventURL,   $eventActive, $eventLocation, $eventID));

    for ($i = 0; $i < count($dateTimes); $i++) {
      $convertedTime = new DateTime($dateTimes[$i]);
      $sql = $connection->prepare('CALL sp_updateTime(?, ?)');
      $sql->execute(array($results[0]['eventID'], $convertedTime->format('Y-m-d H:i:s')));
    }
    header('Location: admin.php');
  }




function getHours($selectId) {
  $times = array();
  for ($i = 1; $i <= 12; $i++) {
    $times[] = "<option>$i</option>";
  }
  return "<select id=\"$selectId\" name=\"$selectId\"><option value=\"\">Select Hour</option>" . implode("\n", $times) . "</select>";
}

function getDays() {
  $namedDays = array(
      'Sunday',
      'Monday',
      'Tuesday',
      'Wednesday',
      'Thursday',
      'Friday',
      'Saturday'
  );

  for ($i = 0; $i < count($namedDays); $i++) {
    return "<select><option value=\"\">Select Day</option><option>" . implode("</option><option>", $namedDays) . "</option></select>";
  }
}

function getMins($selectId) {
  $minutes = array();
  for ($i = 0; $i <= 55; $i+= 5) {
    $minutes[]= "<option>$i</option>";
  }
    return "<select id=\"$selectId\" name=\"$selectId\"><option value=\"\">Select Minute</option>" . implode("\n", $minutes) . "</select>";
}

function getIntDays($selectId) {
  $intDays = array();
  for ($i = 1; $i <= 31; $i +=1) {
    $intDays[] = "<option>$i</option>";
  }
  return "<select id=\"$selectId\" name=\"$selectId\"><option value=\"\">Select Day</option>" . implode("\n", $intDays) . "</select>";
}

function getIntMonth($selectId) {
  $intMonth = array();
  $namedMonth = array(
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
  );

  for ($i = 1; $i <= 12; $i +=1) {
    $intMonth[] = "<option value=\"$i\">${namedMonth[$i-1]}</option>";
  }
  return "<select id=\"$selectId\" name=\"$selectId\"><option value=\"\">Select Month</option>" . implode("\n", $intMonth) . "</select>";
}
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Updating . . .</title>
    <link rel="stylesheet" href="css/master.css?v=2.01">
    <script src="scripts/tinymce/jquery.tinymce.min.js" charset="utf-8"></script>
    <script src="scripts/tinymce/tinymce.min.js" charset="utf-8"></script>
    <script>tinymce.init({ selector:'textarea' });</script>
  </head>
  <body>
<?php
  foreach($msgs as $msg) {
    echo $msg;
  }
?>
    <fieldset>
      <legend>Event</legend>
    <form class="admin-form" action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
      <dl>
        <dt><label for="title">Title</label></dt>
        <dd><input type="text" id="title" name="title" placeholder="Title" value="<?=$results[0]['title'];?>"></dd>
        <dt><label for="description">Description</label></dt>
        <dd><textarea id="description" name="description" rows="8" cols="80"><?=$results[0]['description']?></textarea></dd>
        <dt><label for="select-menu">Date Time</label></dt>
        <dd><select id="select-menu" name="timeAndDate[]" class="select-menu" multiple size="12">
        <?php
        if (!empty($results[1]['eventTime'])) {
          for ($i = 0; $i < count($results); $i++) {
            echo "<option>{$results[$i]['eventTime']}</option>";
          }
        } else {
          echo "<option>{$results[0]['eventTime']}</option>";
        }
        ?>
      </dd></select>
        <button type="button" id="add" name="button">+</button>
        <button type="button" id="minus" name="button">-</button>


        <dt><label for="url">URL</label></dt>
        <dd><input type="text" id="url" name="url" placeholder="http://awesome-event.com" value="<?=$results[0]['url']?>"></dd>
        <dt><label for="location">Location</label></dt>
        <dd><input type="text" name="location" id="location" value="<?=$results[0]['location']?>"></dd>
        <dt><label for="active">Active</label></dt>
        <dd><input type="checkbox" id="active" name="active"
          <?php //check for what value $results holds, give checkbox correct default
          if ($results[0]['active'] == 1) {
            echo "checked";
          } else {
            echo "";
          }
          ?>
          ></dd>

    <input type="hidden" name="didSubmit" value="<?=$results[0]['eventID']?>">
    <button type="submit">Submit</button>
      </dl>
    </form>
    </fieldset>

    <fieldset id="dateAndTime" class="hidden">
      <legend>Date &amp; Time</legend>
      <form class="time" action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
        <fieldset>
          <legend>Time</legend>
          <table>
            <thead>
              <tr>
                <th>Start/End</th><th>HR</th><th>Min</th><th>AM/PM</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Start Time</td>
                <td><?=getHours("startHr")?></td>
                <td><?=getMins("startMin")?></td>
                <td><label for="amStart"><input id="amStart" type="radio" name="ampmStart" value="am">AM</label>
                  <label for="pmStart"><input id="pmStart" type="radio" name="ampmStart" value="pm">PM</label></td>
              </tr>
              <tr>
                <td>End Time</td>
                <td><?=getHours("endHr")?></td>
                <td><?=getMins("endMin")?></td>
                <td><label for="amEnd"><input id="amEnd" type="radio" name="ampmEnd" value="am">AM</label>
                  <label for="pmEnd"><input id="pmEnd" type="radio" name="ampmEnd" value="pm">PM</label></td>
              </tr>
            </tbody>
          </table>
        </fieldset>

        <fieldset>
          <legend>Date</legend>
          <table>
            <thead>
              <tr>
                <th>Start/End</th><th>Day</th><th>Month</th><th>Year</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Start Date</td>
                <td><?=getIntDays("startDay")?></td>
                <td><?=getIntMonth("startMonth")?></td>
                <td>
                  <select id="startYear" name="startYear">
                    <option value="2017">2017</option>
                    <option value="2018">2018</option>
                    <option value="2019">2019</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>End Date</td>
                <td><?=getIntDays("endDay")?></td>
                <td><?=getIntMonth("endMonth")?></td>
                <td>
                  <select id="endYear" name="endYear">
                    <option value="2017">2017</option>
                    <option value="2018">2018</option>
                    <option value="2019">2019</option>
                  </select>
                </td>
              </tr>
            </tbody>
        </table>
        <button id="addDateTime" type="button" name="addDateTime">Add</button>
        <button id="cancelDate" type="button" name="cancelDate">Cancel</button>
        </fieldset>

      </form>
    </fieldset>
    <a href="logout.php">Logout?</a>
    <a href="index.php">Calendar</a>
    <script type="text/javascript" src="scripts/main.js?v=1.15"></script>
  </body>
</html>
