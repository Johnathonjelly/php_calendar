<?php
//Author: Johnathon Southworth
//Class: CS296 PHP Jeff Miller
//Lab: 3 -- calendar
require_once('includes/dbconn.php');
session_start();
$msgs = array();
if (isset($_SESSION['uid'])) {
  $uid = $_SESSION['uid'];
} else {
  header('Location: login.php');
}

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

//if no msgs in msg array we are clear to do stored procedures
    if (count($msgs) === 0) {
      $connect = new Connection;
      $connection = $connect->getConnection();
      $sql = $connection->prepare('CALL sp_addEvent(?, ?, ?, ?, ?)');
      $sql->execute(array($eventTitle, $eventDescription, $eventURL,   $eventActive, $eventLocation));
      $results = $sql->fetch();
      $eventID = $results['eventID'];

      for ($i = 0; $i < count($dateTimes); $i++) {
        $convertedTime = new DateTime($dateTimes[$i]);
        $sql = $connection->prepare('CALL sp_addTime(?, ?)');
        $sql->execute(array($eventID, $convertedTime->format('Y-m-d H:i:s')));
      }
    }
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
    <title>Admin</title>
    <link rel="stylesheet" href="css/master.css?v=1.10">
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
        <dd><input type="text" id="title" name="title" placeholder="Title"></dd>
        <dt><label for="description">Description</label></dt>
        <dd><textarea id="description" name="description" rows="8" cols="80"></textarea></dd>
        <dt><label for="select-menu">Date Time</label></dt>
        <dd><select id="select-menu" name="timeAndDate[]" class="select-menu" multiple size="12"></dd></select>
        <button type="button" id="add" name="button">+</button>
        <button type="button" id="minus" name="button">-</button>


        <dt><label for="url">URL</label></dt>
        <dd><input type="text" id="url" name="url" placeholder="http://awesome-event.com"></dd>
        <dt><label for="location">Location</label></dt>
        <dd><input type="text" name="location" id="location"></dd>
        <dt><label for="active">Active</label></dt>
        <dd><input type="checkbox" id="active" name="active"></dd>

    <input type="hidden" name="didSubmit" value="1">
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
<button type="button" class="allEvents" name="activeEvents">Show All Events</button>
<!--insert a table head and append info from DB in a loop populating the rows-->
<table id="eventInfoTable" class="hidden">
  <thead>
    <tr>
      <th>Title</th>
      <th>Description</th>
      <th>Location</th>
      <th>URL</th>
      <th>Time</th>
      <th>Active</th>
      <th>Action</th>
    </tr>
    </thead>
    <tbody>

    <?php
    //append all events to a table
    $connect = new Connection;
    $connection = $connect->getConnection();
    $sql = $connection->prepare('CALL sp_getAllEvents()');
    $sql->execute();
    $results = $sql->fetchAll();
//sanatize all data
    foreach ($results as $info) {
    $eventDesc = htmlentities(isset($info["description"]) ? $info["description"] : "");
    $eventLocations = htmlentities(isset($info["location"]) ? $info["location"] : "");
    $eventURLs = htmlentities(isset($info["url"]) ? $info["url"] : "");
    $eventTimes = htmlentities(isset($info["eventTime"]) ? $info["eventTime"] : "");
    $eventActiveORNot = htmlentities(isset($info["active"]) ? $info["active"] : "");
    $eventTitles = htmlentities(isset($info["title"]) ? $info["title"] : "");
      echo "<tr><td>{$eventTitles}</td>
      <td>{$eventDesc}</td>
      <td>{$eventLocations}</td>
      <td>{$eventURLs}</td>
      <td>{$eventTimes}</td>
      <td>{$eventActiveORNot}</td>
      <td><a href='update.php?eventID={$info['eventID']}'>Update</a> ||
      <a href='delete.php?eventID={$info['eventID']}'>Delete</a></td></tr>";
    }
     ?>
   </tbody>
</table>
    <a href="logout.php">Logout?</a>
    <a href="index.php">Calendar</a>
    <script type="text/javascript" src="scripts/main.js?v=1.13"></script>
  </body>
</html>
