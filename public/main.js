


function editor_mode(em) {
    edit_mode = em;
    if(edit_mode){
        // document.getElementById('editor_mode').style.color = 'red';
        document.getElementById('addResource').style.display = 'inline';
        document.getElementById('editor_mode_off').style.display = 'inline';
        document.getElementById('editor_mode_on').style.display = 'none';
    } else {
        // document.getElementById('editor_mode').style.color = 'black';
        document.getElementById('addResource').style.display = 'none';
        document.getElementById('editor_mode_off').style.display = 'none';
        document.getElementById('editor_mode_on').style.display = 'inline';
    }
}
function egrid_mode(em) {
    grid_mode = em;
    if(grid_mode){
        document.getElementById('grid_mode_off').style.display = 'inline';
        document.getElementById('grid_mode_allno').style.display = 'inline';
        document.getElementById('grid_mode_allyes').style.display = 'inline';
        document.getElementById('grid_mode_save').style.display = 'inline';
        document.getElementById('grid_mode_on').style.display = 'none';
    } else {
        document.getElementById('grid_mode_off').style.display = 'none';
        document.getElementById('grid_mode_allno').style.display = 'none';
        document.getElementById('grid_mode_allyes').style.display = 'none';
        document.getElementById('grid_mode_save').style.display = 'none';
        document.getElementById('grid_mode_on').style.display = 'inline';
    }
}
function egrid_all(em) {
    for (let i = 0; i < grid.length; i++) {
        for (let j = 0; j < grid[i].length; j++) {
            grid[i][j] = em;
        }
    }
}
function egrid_save() {
    //save new position
    // console.log('new position', clickPos);
    $.post({
        url: "/api/land/grid/save",
        data: {
            land_id : land_id,
            grid: grid
        },
    }).done(function(resp) {
        console.log("Grid saved");
    });
}




function land_go_select() {
    //ajax call to /api/land/list
    $.ajax({
        url: "/api/land/list",
    }).done(function(resp) {
        // console.log("resp: ", resp);
        isPopupVisible = true;
        let html_form = 'Select land<form action="/land/go" method="get">';
        for (let i = 0; i < resp.length; i++) {
            html_form += '#' + resp[i].id + '- <a href="/land/go?id=' + resp[i].id + '">' + resp[i].name + '</a><br>';
        }
        document.getElementById('popup-text').innerHTML = html_form;
        document.getElementById('popup').style.display = 'block';
    });
}

function select_service(farm_id) {

    // console.log('services len', Object.values(farmsServiceArray[farm_id]).length);
    // farmsServiceArray[farm_id].length);
    // farmsServiceArrayLen = Object.values(farmsServiceArray[farm_id]).length;

    $.ajax({
        url: "/api/service-use/select?farm_id=" + farm_id,
    }).done(function(resp) {
        // console.log("resp: ", resp);
        // console.log("resourceArray[farmsArray[farm_id].resource_id]: ", resourceArray[farmsArray[farm_id].resource_id]);
        // console.log('serviceArray', resp.services);

        let html = '';

        if(!resourceArray[farmsArray[farm_id].resource_id].amountable){
            for (let i = 0; i < resp.services.length; i++) {
                // Object.keys(farmsServiceArray[farm_id]).forEach(i => {
                console.log('not amountable', resp.services[i]);
                // console.log('resource_id', farmsArray[farm_id].resource_id);
                html += '<div class="service_item" ' +
                    // 'style="background-image: url(/storage/' + currencyArray[resp.services[i].revenue[0].resource].img + ');" ' +
                    ' onclick = "start(' + farm_id + ',' + resp.services[i].id + ')"' +
                    '>';
                html += 'x '+resp.services[i].revenue[0].value+'<br>'
                html += currencyArray[resp.services[i].revenue[0].resource].name + ' ';

                html += '<img src="/storage/' + currencyArray[resp.services[i].revenue[0].resource].img + '" width="70" height="70">';
                html += '<span><img src="/storage/' + currencyArray[resp.services[i].cost[0].resource].img + '" width="20" height="20"> ';
                html += resp.services[i].cost[0].value+' '
                html += currencyArray[resp.services[i].cost[0].resource].name + '</span><br>';
                if(resp.services[i].cost[1]){
                    html += '<span><img src="/storage/' + currencyArray[resp.services[i].cost[1].resource].img + '" width="20" height="20"> ';
                    html += resp.services[i].cost[1].value+' '
                    html += currencyArray[resp.services[i].cost[1].resource].name + '</span>';
                }


                // html += '- <span>' + resp.services[i].name + '</span>';
                html +='</div>';
                // html += '- <span onclick = "start(' + farm_id + ',' + resp.services[i].id + ')">' + resp.services[i].name + '</span><br>';
            }
        } else {
            for (let i = 0; i < resp.services.length; i++) {
                // Object.keys(farmsServiceArray[farm_id]).forEach(i => {
                console.log('amountable', resp.services[i]);
                // console.log('resource_id', farmsArray[farm_id].resource_id);
                html += '<div class="service_item" ' +
                    // 'style="background-image: url(/storage/' + currencyArray[resp.services[i].revenue[0].resource].img + ');" ' +
                    ' onclick = "select_amount(' + farm_id + ',' + resp.services[i].id + ')"' +
                    '>';
                html += 'x '+resp.services[i].revenue[0].value+'<br>'
                html += currencyArray[resp.services[i].revenue[0].resource].name + ' ';

                html += '<img src="/storage/' + currencyArray[resp.services[i].revenue[0].resource].img + '" width="70" height="70">';
                html += '<span><img src="/storage/' + currencyArray[resp.services[i].cost[0].resource].img + '" width="20" height="20"> ';
                html += resp.services[i].cost[0].value+' '
                html += currencyArray[resp.services[i].cost[0].resource].name + '</span><br>';
                if(resp.services[i].cost[1]){
                    html += '<span><img src="/storage/' + currencyArray[resp.services[i].cost[1].resource].img + '" width="20" height="20"> ';
                    html += resp.services[i].cost[1].value+' '
                    html += currencyArray[resp.services[i].cost[1].resource].name + '</span>';
                }
                // html += '- <span>' + resp.services[i].name + '</span>';
                html +='</div>';
            }
        }
        // console.log('html', html);

        isPopupVisible = true;
        document.getElementById('popup-text').innerHTML = html;
        document.getElementById('popup').style.display = 'block';


    });



}

function select_amount(farm_id,service_id) {


    let html = '';

    //if exchnage sell add price field
    if(farmsArray[farm_id].resource_id == 6){
        html += 'Price: <input type="number" id="price" name="price" value="1" min="1" max="1000"> (max 1000 Coins)<br>';
    }

    html += 'Amount<input type="number" id="amount" name="amount" value="1" min="1" max="100"> (max 100 items)<br>';
    html += '<button onclick="start_amount(' + farm_id + ',' + service_id + ')">Start</button>';

    //back btn
    html += '<br><button onclick="select_service(' + farm_id + ')">Back</button>';

    document.getElementById('popup-text').innerHTML = html;


    // console.log('services len', Object.values(farmsServiceArray[farm_id]).length);
    // farmsServiceArray[farm_id].length);
    // farmsServiceArrayLen = Object.values(farmsServiceArray[farm_id]).length;

    // $.ajax({
    //     url: "/api/service-use/select?farm_id=" + farm_id,
    // }).done(function(resp) {
    // console.log("resp: ", resp);
    // console.log("resourceArray[farmsArray[farm_id].resource_id]: ", resourceArray[farmsArray[farm_id].resource_id]);
    // console.log('serviceArray', resp.services);

    // let html = '';
    //
    // if(!resourceArray[farmsArray[farm_id].resource_id].amountable){
    //     for (let i = 0; i < resp.services.length; i++) {
    //         // Object.keys(farmsServiceArray[farm_id]).forEach(i => {
    //         // console.log('ssssssss', resp.services[i]);
    //         // console.log('resource_id', farmsArray[farm_id].resource_id);
    //         html += '- <span onclick = "start(' + farm_id + ',' + resp.services[i].id + ')">' + resp.services[i].name + '</span><br>';
    //     }
    // } else {
    //     for (let i = 0; i < resp.services.length; i++) {
    //         // Object.keys(farmsServiceArray[farm_id]).forEach(i => {
    //         // console.log('ssssssss', resp.services[i]);
    //         // console.log('resource_id', farmsArray[farm_id].resource_id);
    //         html += '- <span onclick = "amount(' + farm_id + ',' + resp.services[i].id + ')">' + resp.services[i].name + '</span><br>';
    //     }
    // }
    // console.log('html', html);

    // isPopupVisible = true;
    // document.getElementById('popup-text').innerHTML = html;
    // document.getElementById('popup').style.display = 'block';


    // });



}

function start_amount(farm_id,service_id) {
    // window.location.href = '/service-use/claim?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id;


    console.log('selected amount:', $('#amount').val());
    //return;

    let urlaction;

    if(farmsArray[farm_id].resource_id == 6){
        urlaction= "/api/service-use/sell?farm_id=" + farm_id + "&service_id=" + service_id + "&amount=" + $('#amount').val() + "&price=" + $('#price').val();
    } else {
        urlaction= "/api/service-use/claim?farm_id=" + farm_id + "&service_id=" + service_id + "&amount=" + $('#amount').val();
    }


    $.ajax({
        url: urlaction
    }).done(function(resp) {
        console.log("resp: ", resp);

        let html = '';
        // if(resp.status == 'success'){
        //     if(serviceArray[service_id].time>0){
        //         farmsArray[farm_id].status = 'in_use';
        //         farmsArray[farm_id].text = serviceArray[service_id].time;
        //         isCooldownActive[farm_id] = true;
        //         cooldownDuration[farm_id] = serviceArray[service_id].time;
        //         cooldownReady[farm_id] = serverTime + serviceArray[service_id].time;
        //     }
        //     if(serviceArray[service_id].reload>0){
        //         farmsArray[farm_id].status = 'reload';
        //         farmsArray[farm_id].text = serviceArray[service_id].reload;
        //         isCooldownActive[farm_id] = true;
        //         cooldownDuration[farm_id] = serviceArray[service_id].reload;
        //         cooldownReady[farm_id] = serverTime + serviceArray[service_id].reload;
        //     }
        // } else {
        //     //     html += 'Error';
        // }

        // if(resp.message){
        //     html += '<br>' + resp.message;
        // }

        if(resp.extra){
            html += '<br>' + resp.extra;
        }

        // isPopupVisible = true;

        document.getElementById('popup-text').innerHTML = html;
        // document.getElementById('popup').style.display = 'block';

        // console.log('serviceArray', serviceArray[service_id]);
        //
        setTimeout(() => {
            // document.getElementById('popup').style.display = 'none';
            // isPopupVisible = false;
            select_service(farm_id);

        },2000);
        document.getElementById('balance').innerHTML = resp.balance;


    });

}

function start(farm_id,service_id) {
    // window.location.href = '/service-use/claim?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id;
    $.ajax({
        url: "/api/service-use/claim?farm_id=" + farm_id + "&service_id=" + service_id,
    }).done(function(resp) {
        console.log("resp: ", resp);

        let html = '';
        if(resp.status == 'success'){
            //     html += 'Success';
            //     delete farmsServiceArray[farm_id];
            //reload page
            // window.location.reload();
            if(serviceArray[service_id].time>0){
                farmsArray[farm_id].status = 'in_use';
                farmsArray[farm_id].text = serviceArray[service_id].time;
                isCooldownActive[farm_id] = true;
                cooldownDuration[farm_id] = serviceArray[service_id].time;
                cooldownReady[farm_id] = serverTime + serviceArray[service_id].time;
            }
            if(serviceArray[service_id].reload>0){
                farmsArray[farm_id].status = 'reload';
                farmsArray[farm_id].text = serviceArray[service_id].reload;
                isCooldownActive[farm_id] = true;
                cooldownDuration[farm_id] = serviceArray[service_id].reload;
                cooldownReady[farm_id] = serverTime + serviceArray[service_id].reload;
            }
        } else {
            //     html += 'Error';
        }

        // if(resp.message){
        //     html += '<br>' + resp.message;
        // }

        // if(resp.extra){
            html += '<br>' + resp.extra;
        // }

        isPopupVisible = true;

        document.getElementById('popup-text').innerHTML = html;
        document.getElementById('popup').style.display = 'block';

        setTimeout(() => {
            document.getElementById('popup').style.display = 'none';
            isPopupVisible = false;
        },2000);

        //not enoth balance not return balance
        if(resp.balance){
            document.getElementById('balance').innerHTML = resp.balance;
        }


    });
}

function start_with(farm_id,currency_id) {
    console.log('start_with');
    //farm id
    console.log(farm_id);
    console.log(currency_id);
}

function claim(farm_id,service_id) {
    // window.location.href = '/service-use/claim?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id;
    $.ajax({
        url: "/api/service-use/claim?farm_id=" + farm_id + "&service_id=" + service_id,
    }).done(function(resp) {
        console.log("resp: ", resp);

        let html = '';

        // if(resp.extra){
            html += '<br>' + resp.extra;
        // }

        document.getElementById('popup-text').innerHTML = html;
        document.getElementById('popup').style.display = 'block';

        if(serviceArray[service_id].reload>0){
            farmsArray[farm_id].status = 'reload';
            farmsArray[farm_id].text = serviceArray[service_id].reload;
            isCooldownActive[farm_id] = true;
            cooldownDuration[farm_id] = farmsArray[farm_id].text;
        } else {
            farmsArray[farm_id].status = 'start';
            farmsArray[farm_id].text = 'Start';
        }

        setTimeout(() => {
            document.getElementById('popup').style.display = 'none';
        },2000);

        // if(resp.balance) {
            document.getElementById('balance').innerHTML = resp.balance;
        // }

    });
}

function move_farm(clickPos) {
    //save new position
    // console.log('new position', clickPos);
    $.ajax({
        url: "/farm/move?farm_id=" + currentlyDraggingFarm + "&x=" + clickPos.x + "&y=" + clickPos.y,
    }).done(function(resp) {
        // console.log("resp: ", resp);
        farmsObjectPos[currentlyDraggingFarm].x = clickPos.x;
        farmsObjectPos[currentlyDraggingFarm].y = clickPos.y;
        currentlyDraggingFarm = null; // Stop dragging
    });
}

function set_farm(clickPos) {

    console.log('set_farm');

    //check id currency can build farm
    console.log(currencyArray[currentlyDraggingResource]);
    if(!currencyArray[currentlyDraggingResource].resource_id){
        console.log('currency not buildable');
        // isPopupVisible = true;
        //
        // document.getElementById('popup-text').innerHTML = 'Currency not buildable';
        // document.getElementById('popup').style.display = 'block';
        //
        // setTimeout(() => {
        //     document.getElementById('popup').style.display = 'none';
        //     isPopupVisible = false;
        // },2000);

    } else {
        console.log('currency buildable',clickPos.x,clickPos.y);
        window.location.href = '/farm/set?currency_id=' + currentlyDraggingResource + '&x=' + clickPos.x + '&y=' + clickPos.y;
    }





    // $.ajax({
    //     url: "/farm/set?currency_id=" + currentlyDraggingResource + "&x=" + clickPos.x + "&y=" + clickPos.y,
    // }).done(function(resp) {
    //     // console.log("resp: ", resp);
    //     farmsObjectPos[currentlyDragging].x = clickPos.x;
    //     farmsObjectPos[currentlyDragging].y = clickPos.y;
    //     currentlyDragging = null; // Stop dragging
    // });


}


/*
function pick_farm() {

    console.log('pick_farm');

    //check id currency can build farm
    // console.log(currencyArray[currentlyDragging]);
    // if(!currencyArray[currentlyDragging].resource_id){
    //     console.log('currency not pickable');
    //     // isPopupVisible = true;
    //     //
    //     // document.getElementById('popup-text').innerHTML = 'Currency not buildable';
    //     // document.getElementById('popup').style.display = 'block';
    //     //
    //     // setTimeout(() => {
    //     //     document.getElementById('popup').style.display = 'none';
    //     //     isPopupVisible = false;
    //     // },2000);
    //
    // } else {
        console.log('farm pickable',currentlyDragging);
        // window.location.href = '/farm/pick?currency_id=' + currentlyDragging;
    // }





    // $.ajax({
    //     url: "/farm/set?currency_id=" + currentlyDraggingResource + "&x=" + clickPos.x + "&y=" + clickPos.y,
    // }).done(function(resp) {
    //     // console.log("resp: ", resp);
    //     farmsObjectPos[currentlyDragging].x = clickPos.x;
    //     farmsObjectPos[currentlyDragging].y = clickPos.y;
    //     currentlyDragging = null; // Stop dragging
    // });


}
*/


// function goBack() {
//     // Functionality for the 'Back' button
//     window.history.back(); // This simply navigates to the previous page in the browser history
// }



//select clicke resource
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



document.getElementById('balance').style.display = 'block';


if(land_owner){
    document.getElementById('editor_mode_on').style.display = 'inline';

    $('.resources').click(function(){

        if(!currentlyDraggingResource) {
            console.log($(this).data('id'));
            currentlyDraggingResource = $(this).data('id');
        } else {
            currentlyDraggingResource = null;
        }

    });

    $('#pickResource').click(function(){

        if(currentlyDraggingFarm){
            console.log('pick up');
            // pick_farm();
            console.log('farm pickable',currentlyDraggingFarm);
            window.location.href = '/farm/pick?farm_id=' + currentlyDraggingFarm;

        }

    });
}
if(user_id == 1){
    document.getElementById('grid_mode_on').style.display = 'inline';
}


if (!!window.EventSource) {
    var source = new EventSource("/stream?land_id="+land_id);
    source.onmessage = function(event) {
        var data = JSON.parse(event.data);
        serverTime = data.serverTime;
        usersArray = data.usersArray;
    };
} else {
    console.log("Your browser does not support server-sent events.");
}




setInterval(function() {


    if(!isPopupVisible){
        $.ajax({
            url: "/position/go?x=" + heroPos.x + "&y=" + heroPos.y,
            // context: document.body
        });
    }


        // .done(function(resp) {
        //     // $( this ).addClass( "done" );
        //     console.log("resp: ", resp);
        // });
}, 5000);
