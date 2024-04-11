document.getElementsByClassName('closeBtn')[0].onclick = function() {
    document.getElementById('popup').style.display = 'none';
    isPopupVisible = false;
}

// script for popup
// Close the popup if the user clicks anywhere outside of it
// window.onclick = function(event) {
//     if (event.target == document.getElementById('popup')) {
//         document.getElementById('popup').style.display = 'none';
//         isPopupVisible = false;
//     }
// }

if (!!window.EventSource) {
    var source = new EventSource("/stream");
    source.onmessage = function(event) {
        var data = JSON.parse(event.data);
        serverTime = data.serverTime;
        usersArray = data.usersArray;
    };
} else {
    console.log("Your browser does not support server-sent events.");
}


if(land_owner){
    document.getElementById('editor_mode_on').style.display = 'block';
}


function editor_mode(em) {

    edit_mode = em;
    // editor_mode = em;
    console.log('editor_mode');
    //read from local storage
    // let edit_mode = localStorage.getItem('edit_mode');
    console.log(edit_mode);
    if(edit_mode){
        // document.getElementById('editor_mode').style.color = 'red';
        document.getElementById('addResource').style.display = 'block';
        document.getElementById('editor_mode_off').style.display = 'block';
        document.getElementById('editor_mode_on').style.display = 'none';
    } else {
        // document.getElementById('editor_mode').style.color = 'black';
        document.getElementById('addResource').style.display = 'none';
        document.getElementById('editor_mode_off').style.display = 'none';
        document.getElementById('editor_mode_on').style.display = 'block';
    }
    // edit_mode = !edit_mode;
    //save to local storage
    // localStorage.setItem('edit_mode', edit_mode);
}




