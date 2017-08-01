(function() {

  //grab all the buttons and stuff I'll need
  const addButton = document.getElementById('add');
  const subButton = document.getElementById('minus');
  const dateAndTime = document.getElementById('dateAndTime');
  const addDateTimes = document.getElementById('addDateTime');
  const cancelDate = document.getElementById('cancelDate');
  const amStart = document.getElementById('amStart');
  const amEnd = document.getElementById('amEnd');
  const pmStart = document.getElementById('pmStart');
  const pmEnd = document.getElementById('pmEnd');
  const startHr = document.getElementById('startHr');
  const startMin = document.getElementById('startMin');
  const endHr = document.getElementById('endHr');
  const endMin = document.getElementById('endMin');
  const startDay = document.getElementById('startDay');
  const startMonth = document.getElementById('startMonth');
  const endMonth = document.getElementById('endMonth');
  const endDay = document.getElementById('endDay');
  const startYear = document.getElementById('startYear');
  const endYear = document.getElementById('endYear');
  const selectMenu = document.getElementById('select-menu');
  const allEventsButton = document.querySelector('.allEvents');
  const eventInfoTable = document.getElementById('eventInfoTable');
  const mainForm = document.forms[0];
  const secondForm = document.forms[1];

  //this removes the class of 'hidden' on dateandTime
  function showDateTime() {
    dateAndTime.className = '';
    console.log('clicked!');
  }

  //this adds the class of 'hidden' to dateAndTime
  function removeDateTime() {
    dateAndTime.className = 'hidden';
  }

  //presses the subtract button on admin.php removes a highlighted select option
  function removeOptions() {
    selectMenu.remove(selectMenu.selectedIndex);
  }


  function dateOption() {
    let timeObj = {};
    const secondFormElements = secondForm.elements;
    for (let i = 0; i < secondFormElements.length; i += 1) {
      let element = secondFormElements[i];
      if (element.type.indexOf('select') != -1) {
        timeObj[element.name] = parseInt(element.options[element.selectedIndex].value, 10);
      }

      amStart.checked ? timeObj['amStart'] = 'true' : timeObj['amStart'] = 'false';
      amEnd.checked ? timeObj['amEnd'] = 'true' : timeObj['amEnd'] = 'false';
      pmStart.checked ? timeObj.startHr += 12 : timeObj['pmStart'] = 'false';
      pmEnd.checked ? timeObj.endHr += 12 : timeObj['pmEnd'] = 'false';

    }
    let startDate = new Date(timeObj.startYear, timeObj.startMonth - 1, timeObj.startDay, timeObj.startHr, timeObj.startMin);
    let endDate = new Date(timeObj.endYear, timeObj.endMonth - 1, timeObj.endDay, timeObj.endHr, timeObj.endMin);

    console.log(timeObj);
    console.log(startDate.toString());
    try {
      let startOption = new Option(startDate.toString(), startDate.toISOString());
      selectMenu.add(startOption);
    } catch (e) {
      window.alert('You must add a valid date time.');
    }

    try {
      let endOption = new Option(endDate.toString(), endDate.toISOString());
      selectMenu.add(endOption);
    } catch (e) {
      timeObj['noEndTime'] = true;
    }






  }
  //highlight all options
  function hightlightEvery() {
    for (let i = 0; i < selectMenu.options.length; i++) {
      selectMenu.options[i].selected = true;
    }
  }

  function showEvents() {
    eventInfoTable.className = '';
    console.log('clicked!');
  }




  subButton.addEventListener('click', removeOptions);
  addButton.addEventListener('click', showDateTime);
  cancelDate.addEventListener('click', removeDateTime);
  addDateTimes.addEventListener('click', dateOption);
  mainForm.addEventListener('submit', hightlightEvery);
  allEventsButton.addEventListener('click', showEvents);



})();
