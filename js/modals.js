
let modal = document.getElementById("myModal");
let fillModal = document.getElementById("modal-text");
let span = document.getElementsByClassName("close")[0];

window.addEventListener('load', (event) => {

    let params = new URLSearchParams(document.location.search);
    let reqStatus = params.get("redirect_status");

    if (reqStatus == 'cash') {
        fillModal.innerHTML = messages_object.cash;
        modal.style.display = "block";

    }

});

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}