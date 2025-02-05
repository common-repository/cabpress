var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
// This function will display the specified tab of the form ...
var x = document.getElementsByClassName("tab");
x[n].style.display = "block";

// ... and fix the Previous/Next buttons:
if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
} else {
    document.getElementById("prevBtn").style.display = "inline";
}
if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "End";
} else if (n == x.length - 2) {
    document.getElementById("nextBtn").style.display = "none";
    document.getElementById("payCash").style.display = "inline";
    document.getElementById("payCash").innerHTML = "Cash pay";
} else {
    document.getElementById("nextBtn").innerHTML = "Next";
    document.getElementById("prevBtn").innerHTML = "Previous";
}
// ... and run a function that displays the correct step indicator:
fixStepIndicator(n)
}

function nextPrev(n) {
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("tab");
    // Exit the function if any field in the current tab is invalid:
    if (n == 1 && !validateForm()) return false;
    // Hide the current tab:
    x[currentTab].style.display = "none";
    // Increase or decrease the current tab by 1:
    currentTab = currentTab + n;
    getFormData();

    if (currentTab == 1) {
        addCarSelection();

        // force car selection to access next page
        let b = document.getElementById("nextBtn");
        b.disabled = true;

        let carInput = document.querySelectorAll('input[name="total-price"]');                    // If a field is empty...
        carInput.forEach(changeButton);
        
        function changeButton ( item) {
            item.addEventListener('change', function () {b.disabled = false;});
        }
        
    }
    // if you have reached the end of the form... :
    if (currentTab == x.length) {
        //...the form gets submitted:
        document.getElementById("regForm").submit();
        return false;
    }
    
    // Otherwise, display the correct tab:
    //if none of them is checked display a error message
        showTab(currentTab);

    
}

function validateForm() {
// This function deals with validation of the form fields
var x, y, i, valid = true;
x = document.getElementsByClassName("tab");
y = x[currentTab].getElementsByTagName("input");

// A loop that checks every input field in the current tab:
for (i = 0; i < y.length; i++) {

    if (y[i].getAttribute("name") !== "luggage") {
        if (y[i].value == "" ) {
            // add an "invalid" class to the field:
            y[i].className += " invalid";
            // and set the current valid status to false:
            valid = false;
        }
    }
}
// If the valid status is true, mark the step as finished and valid:
if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
}
    return valid; // return the valid status
}

function fixStepIndicator(n) {
// This function removes the "active" class of all steps...
var i, x = document.getElementsByClassName("step");
for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
}
//... and adds the "active" class to the current step:
x[n].className += " active";
}


