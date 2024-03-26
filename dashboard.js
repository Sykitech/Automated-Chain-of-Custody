//charts//
var barchartoptions = {
    series: [{
    data: [20, 15, 8, 4, 2]
  }],
    chart: {
    type: 'bar',
    height: 350,
    toolbar:{
        show: false
    }
  },
  colors:[
    "#a57c00",
    "#191C40",
    "#a57c00",
    "#191C40",
    "#a57c00"
  ],
  plotOptions: {
    bar: {
        distributed:true,
      borderRadius: 4,
      horizontal: false,
      columnwidth:"40%",
    }
  },
  dataLabels: {
    enabled: false
  },
  legend :{
    show: false
  },
  xaxis: {
    categories: ['Drugs', 'Bodily fluids', 'cellular devices', 'finger prints', 'others'
    ],
  },
  yaxis:{
    title:{
       text: "count"
    }
  }
  };

  var barchart = new ApexCharts(document.querySelector("#bar_chart"), barchartoptions);
  barchart.render();

  //area charts//

  var areachartoptions = {
    series: [{
    name: 'In items',
    data: [44, 55, 31, 47, 31, 43, 26, 41, 31, 47, 33, 62]
  }, {
    name: 'Disposed',
    data: [25, 15, 35, 30, 43, 24, 37, 42, 34, 21, 43, 20]
  }],
    chart: {
    height: 350,
    type: 'area',
    toolbar:{
        show:false,
    },
  },
  colors:[
    "#a57c00",
    "#191C40",
  ],
  dataLabels:{
    enabled: false,
  },
  stroke: {
    curve: 'smooth'
  },
  labels: ['Jan', 'Feb','March','April','May','June','July','Aug','Sept','Oct','Nov','Dec'],
  markers: {
    size: 0
  },
  yaxis: [
    {
      title: {
        text: 'In items',
      },
    },
    {
      opposite: true,
      title: {
        text: 'Disposed',
      },
    },
  ],
  tooltip: {
    shared: true,
    intersect: false,
  }
  };

  var areachart = new ApexCharts(document.querySelector("#area_chart"), areachartoptions);
  areachart.render();

// Function to toggle the display of the dropdown content
function toggleDropdown(dropdownId) {
    var dropdownContent = document.getElementById(dropdownId);
    dropdownContent.style.display = (dropdownContent.style.display === "block") ? "none" : "block";
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.dropdown-btn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.style.display === "block") {
                openDropdown.style.display = "none";
            }
        }
    }
}


// Function to select a dropdown item and update the input field
function selectDropdownItem(item) {
    var inputElement = document.getElementById('personTypeInput');
    if (inputElement) {
        inputElement.value = item.textContent;
    }

    // Close the dropdown after selecting an item
    toggleDropdown('mydropdown');
}

// Close the dropdown if the user clicks outside of it
window.addEventListener('click', function(event) {
    if (!event.target.matches('.dropdown-mbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-mcontent");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.style.display === "block") {
                openDropdown.style.display = "none";
            }
        }
    }
});

// Function to select a custody input dropdown item and update the input field
function selectDropdownItem(item) {
    var inputElement = document.getElementById('custodyReasonInput');
    if (inputElement) {
        inputElement.value = item.textContent;
    }

    // Close the dropdown after selecting an item
    toggleDropdown('custodydropdown');
}

// Close the dropdown if the user clicks outside of it
window.addEventListener('click', function(event) {
    if (!event.target.matches('.dropdown-cbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-ccontent");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.style.display === "block") {
                openDropdown.style.display = "none";
            }
        }
    }
});

// get cases

$(document).ready(function () {
    // Function to handle hover and fetch existing cases
    $("#case_number").hover(function () {
        // Make an AJAX request to get existing cases
        $.ajax({
            type: "POST",
            url: "get_cases.php",
            data: { get_cases: 1 },
            dataType: "json",
            success: function (response) {
                // Display the list of cases
                if (response.length > 0) {
                    var casesList = response.join(', ');
                    alert("Existing Cases: " + casesList);
                } else {
                    alert("No existing cases found.");
                }
            },
            error: function () {
                alert("Error fetching existing cases.");
            }
        });
    });
});
//get persons

$(document).ready(function () {
  // Event listener for the "Belongs to" input
  $("#belongs_to").focus(function () {
      // Make an AJAX request to get existing persons
      $.ajax({
          type: "POST",
          url: "get_persons.php",
          data: { get_persons: 1 },
          dataType: "json",
          success: function (response) {
              // Display the list of persons
              if (response.length > 0) {
                  var personsList = response.join(', ');
                  alert("Existing Persons: " + personsList);
              } else {
                  alert("No existing persons found.");
              }
          },
          error: function () {
              alert("Error fetching existing persons.");
          }
      });
  });
});

// Function to toggle item sharing
function toggleItemSharing() {
  // Toggle the state
  isItemSharingOn = !isItemSharingOn;

  // Update the button and icon classes
  const button = document.getElementById('toggleButton');
  const powerIcon = document.getElementById('powerIcon');
  
  if (isItemSharingOn) {
      // If it's on, add the "on" class to the button and change the power icon
      button.classList.add('on');
      powerIcon.classList.remove('fa-power-off');
      powerIcon.classList.add('fa-power-on');
  } else {
      // If it's off, remove the "on" class from the button and change the power icon
      button.classList.remove('on');
      powerIcon.classList.remove('fa-power-on');
      powerIcon.classList.add('fa-power-off');
  }

  // Your logic to perform actions based on the state goes here
  if (isItemSharingOn) {
      // If it's on, perform actions for turning it on
      alert('Item Sharing is turned ON');
  } else {
      // If it's off, perform actions for turning it off
      alert('Item Sharing is turned OFF');
  }
}

