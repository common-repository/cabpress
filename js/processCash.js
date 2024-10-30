let checkInputs;

document.getElementById('payCash').addEventListener("click", function () {
    
    getFormData();
    console.log(itemsDatas);
    let infosDatas = ['prenom', 'nom', 'phone', 'mail', 'passenger'];
    checkInputs = 0;

    infosDatas.forEach(element => {
        //check inputs if empty focus and display placehoder
        if (itemsDatas[element] == "") {
            //console.log(element);
            document.getElementById(element).focus();
            document.getElementById(element).style.background = "lightsalmon";
            checkInputs ++;
        }
    });
    console.log(checkInputs);

    if (!checkInputs) {
        sendTheMail();
    }

})

function sendTheMail () {

    //console.log(email_file_path_object.url);
    //send Email with info
    let xhr = new XMLHttpRequest();

    xhr.open("POST", email_file_path_object.url, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    
    xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            console.log('response' + xhr.responseText);
        }
    };
    
    xhr.send(JSON.stringify(itemsDatas));
    
    // refirect to the home page and display the modal
    window.location = email_file_path_object.currentDomainUrl + "?redirect_status=cash";

}
