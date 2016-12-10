// Get the modal
var modal1 = document.getElementById('id01');
var modal2 = document.getElementById('id02');
var modal3 = document.getElementById('id03');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal1 || event.target == modal2) {
        modal1.style.display = "none";
        modal2.style.display = "none";
        window.history.pushState("", "", '/');
    }
    else if (event.target == modal3){
        modal3.style.display = "none";
        window.history.pushState("", "", '/profile');
    }
}

