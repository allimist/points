let mousePressedTime = 0;
const moveThreshold = 200;


let speed = 2;
//patch for vip
if(balanceArray[13] >0){
    speed = 5;
}

let heroSize = 70;
let mapWidth = 2000;
let mapHeight = 1000;

let hero;
let heroFlipped; // Variable to store the flipped version of the image
let heroMoveRight = false;

let mapBackground;
let heroPos;
let proposedNewPosition;
// let resorceArray;
// let farmsArray;
// let farmsArrayLen = 0;

farmsArrayLen = Object.values(farmsArray).length;
usersArrayLen = Object.values(usersArray).length;

let interactionDistance = 200;

let farmsObjectPos = [];
let farmsObject = [];
let farmsObjectHover = [];
let farmsObjectSize  = [];
let farmsObjectStatus = [];


let cooldownDuration = [];
let cooldownReady = [];
let startTime;
let isCooldownActive = [];
let timeRemaining = [];

let updateHeroPositionLoop = 0;
let updateHeroPositionInterval = 200;


let heros = [];
let herosPos = [];

// let edit_mode = false;
let currentlyDragging = null; // Track the rectangle currently being dragged



// let fix_x= -50;
// let fix_y= 50;

// let isPopupVisible = false;

function preload() {


    mapBackground = loadImage('storage/'+map);
    // avatarsArray = resp.avatars;
    console.log('avatarsArray', avatarsArray);
    console.log('avatar_id', avatar_id);
    // hero = loadImage(avatarsArray[3].img);
    // hero = loadImage('img/fly-sky.gif');
    hero = loadImage(avatarsArray[avatar_id].img);
    posx = posx;
    posy = posy;


    // farmsArrayLen = Object.values(farmsArray).length;

    Object.keys(farmsArray).forEach( i => {

        if(resourceArray[farmsArray[i].resource_id].img){
            farmsObject[i] = loadImage(resourceArray[farmsArray[i].resource_id].img);
        }else {
            farmsObject[i] = loadImage('clickableObject.png');
        }
        if(resourceArray[farmsArray[i].resource_id].img_hover) {
            farmsObjectHover[i] = loadImage(resourceArray[farmsArray[i].resource_id].img_hover);
        } else {
            farmsObjectHover[i] = loadImage('clickableObject.png');
        }
        // farmsObjectSize[i] = farmsArray[i].size * 10;
        farmsObjectSize[i] = resourceArray[farmsArray[i].resource_id].size * 10;
    });

    // hero = loadImage('img/hero.png');

    console.log('farmsArray', farmsArray);

    heros[1] = loadImage(avatarsArray[1].img);
    heros[2] = loadImage(avatarsArray[2].img);
    heros[3] = loadImage(avatarsArray[3].img);

    Object.keys(usersArray).forEach(i => {
    // console.log('usersArray', usersArray);
    // heros[1] = loadImage(avatarsArray[1].img);
    // heros[2] = loadImage(avatarsArray[2].img);
    // heros[3] = loadImage(avatarsArray[3].img);
        heros[i] = loadImage(avatarsArray[usersArray[i].avatar_id].img);
    // heros[1] = loadImage('hero.png');
    // heros[2] = loadImage('hero.png');
    });

}

function setup() {

    // noLoop();
    // console.log('windowHeight', windowHeight);
    // if(windowHeight > 500){
    //     createCanvas(windowWidth, 500);
    // } else {
        createCanvas(windowWidth, windowHeight);
    // }

    // createCanvas(windowWidth, 500);
    heroFlipped = createGraphics(hero.width, hero.height);
    heroFlipped.scale(-1, 1); // Flip the image horizontally
    heroFlipped.image(hero, -hero.width, 0);

    heroPos = createVector(posx, posy);
    proposedNewPosition = createVector(posx, posy);

    startTime = millis();

    console.log('farmsArray', farmsArray);
    if(farmsArray){
        console.log('farmsArray', farmsArray);
        Object.keys(farmsArray).forEach(i => {
            farmsObjectPos[i] = createVector(farmsArray[i].posx, farmsArray[i].posy);

            if(farmsArray[i].status == 'in_use' || farmsArray[i].status == 'reload'){
                isCooldownActive[i] = true;
                cooldownDuration[i] = farmsArray[i].text;
                cooldownReady[i] = farmsArray[i].ready;
            }

            /*
            if(farmsArray[i].status == 'start' || farmsArray[i].status == 'claim'){

                if (typeof farmsServiceArray[i] !== 'undefined'){
                    if(farmsArray[i].single_service == 1){

                        if(farmsServiceArray[i].name == "Go"){
                            // farmsObjectStatus[i] = createA('/land/select?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id, 'Select').class('p-select');
                        } else {
                            // farmsObjectStatus[i] = createA('/service-use/claim?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id, 'Start').class('p-select');
                        }

                    } else {
                        // farmsObjectStatus[i] = createA('/service-use/select?farm_id=' + farmsArray[i].id , 'Select').class('p-select');
                    }

                } else {
                    // farmsObjectStatus[i] = createA('/service-use/claim?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id, farmsArray[i].text).class('p-claim');

                }
            } else {
                cooldownDuration[i] = farmsArray[i].text
                isCooldownActive[i] = true;
            }

             */

        });
    }



    Object.keys(usersArray).forEach(i => {
    //     console.log('i', i);
    //     console.log('herosPos', herosPos[i]);
        herosPos[i] = createVector(usersArray[i].posx, usersArray[i].posy);
        // herosPos[i] = createVector(posx, posy);
        if(i >10){
            return;
        }

    });

}

function draw() {

    background(220);

    let camX = constrain(heroPos.x - width / 2, 0, mapWidth - width);
    let camY = constrain(heroPos.y - height / 2, 0, mapHeight - height);

    if(mapBackground){
        image(mapBackground, -camX, -camY, mapWidth, mapHeight);
    }

    if(hero && !isPopupVisible){
        updateHeroPosition();
    }


    let currentTime = millis();
    let timeElapsed = (currentTime - startTime)/1000;

    if(farmsArray) {
        Object.keys(farmsArray).forEach(i => {

            if(currentlyDragging === i) {

                image(farmsObject[i], mouseX - farmsObjectSize[i] / 2, mouseY - farmsObjectSize[i] / 2, farmsObjectSize[i], farmsObjectSize[i]);

            } else {

                if (mouseX > farmsObjectPos[i].x - camX - farmsObjectSize[i] / 2 && mouseX < farmsObjectPos[i].x - camX + farmsObjectSize[i] / 2 &&
                    mouseY > farmsObjectPos[i].y - camY - farmsObjectSize[i] / 2 && mouseY < farmsObjectPos[i].y - camY + farmsObjectSize[i] / 2) {
                    // fill('red');

                    // console.log();
                    // farmsObjectHover
                    image(farmsObjectHover[i], farmsObjectPos[i].x - camX - farmsObjectSize[i] / 2, farmsObjectPos[i].y - camY - farmsObjectSize[i] / 2, farmsObjectSize[i], farmsObjectSize[i]);
                    // image(farmsObject[i], farmsObjectPos[i].x - camX - farmsObjectSize[i]/2, farmsObjectPos[i].y - camY - farmsObjectSize[i]/2, farmsObjectSize[i], farmsObjectSize[i]);

                    // console.log('farmsArray[i]', farmsArray[i]);
                } else {
                    fill('black');
                    image(farmsObject[i], farmsObjectPos[i].x - camX - farmsObjectSize[i] / 2, farmsObjectPos[i].y - camY - farmsObjectSize[i] / 2, farmsObjectSize[i], farmsObjectSize[i]);
                }
            }

            // if(farmsArray[i].status == 'in_use'){

                if (isCooldownActive[i]) {
                    // timeRemaining[i] = cooldownDuration[i] - timeElapsed;
                    timeRemaining[i] = cooldownReady[i] - serverTime;
                    if (timeRemaining[i] > 0) {
                        // Display the remaining cooldown time in seconds
                        let timeInSeconds = (timeRemaining[i]).toFixed(0); // Round to one decimal place
                        let hours = Math.floor(timeInSeconds / 3600); // 3600 seconds in an hour
                        let minutes = Math.floor((timeInSeconds % 3600) / 60); // Remaining minutes
                        let seconds = timeInSeconds % 60; // Remaining seconds
                        let formattedHours = hours.toString();//.padStart(2, '0');
                        let formattedMinutes = minutes.toString().padStart(2, '0');
                        let formattedSeconds = seconds.toString().padStart(2, '0');

                        if(farmsArray[i].status == 'in_use') {
                            timeString = `In use ${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                        } else {
                            timeString = `Reload ${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                        }
                        text(timeString, farmsObjectPos[i].x - camX - farmsObjectSize[i]/2 - 50, farmsObjectPos[i].y - camY + farmsObjectSize[i]/2 );
                    } else {
                        if(farmsArray[i].status == 'in_use') {
                            farmsArray[i].status = 'claim';
                            farmsArray[i].text = 'Claim';
                        } else {
                            farmsArray[i].status = 'start';
                            farmsArray[i].text = 'Start';
                        }
                        // Cooldown complete
                        text("Cooldown Complete!", farmsObjectPos[i].x - camX, farmsObjectPos[i].y - camY );

                        isCooldownActive[i] = false; // Stop the cooldown
                    }
                } else {
                    // text("Click to Start Cooldown", width / 2, height / 2);
                }

            // }


            /*


            // Create a clickable link
            if(farmsArray[i].status == 'start' ){//|| farmsArray[i].status == 'claim'){
                // farmsObjectStatus[i].position(farmsObjectPos[i].x - camX + farmsObjectSize[i]/2 + fix_x, farmsObjectPos[i].y - camY + farmsObjectPos[i].y/2 + fix_y);
            } else {
                if (isCooldownActive[i]) {
                    timeRemaining[i] = cooldownDuration[i] - timeElapsed;
                    if (timeRemaining[i] > 0) {
                        // Display the remaining cooldown time in seconds
                        let timeInSeconds = (timeRemaining[i]).toFixed(0); // Round to one decimal place
                        let hours = Math.floor(timeInSeconds / 3600); // 3600 seconds in an hour
                        let minutes = Math.floor((timeInSeconds % 3600) / 60); // Remaining minutes
                        let seconds = timeInSeconds % 60; // Remaining seconds
                        let formattedHours = hours.toString();//.padStart(2, '0');
                        let formattedMinutes = minutes.toString().padStart(2, '0');
                        let formattedSeconds = seconds.toString().padStart(2, '0');

                        if(farmsArray[i].status == 'in_use') {
                            timeString = `In use ${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                        } else {
                            timeString = `Reload ${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                        }
                        text(timeString, farmsObjectPos[i].x - camX - farmsObjectSize[i]/2 - 50, farmsObjectPos[i].y - camY + farmsObjectSize[i]/2 );
                    } else {
                        // Cooldown complete
                        text("Cooldown Complete!", farmsObjectPos[i].x - camX, farmsObjectPos[i].y - camY );
                        isCooldownActive[i] = false; // Stop the cooldown
                    }
                } else {
                    // text("Click to Start Cooldown", width / 2, height / 2);
                }

            }
            */
        });
    }


    heroPos.x = constrain(heroPos.x, 0, mapWidth - heroSize);
    heroPos.y = constrain(heroPos.y, 0, mapHeight - heroSize);
    if(heroMoveRight){
        image(heroFlipped, heroPos.x - camX, heroPos.y - camY, heroSize, heroSize);
    } else {
        image(hero, heroPos.x - camX, heroPos.y - camY, heroSize, heroSize);
    }
    // image(hero, heroPos.x - camX, heroPos.y - camY, heroSize, heroSize);

    //update hero position
    updateHeroPositionLoop++;
    if (updateHeroPositionLoop >= updateHeroPositionInterval) {
        updateHeroPositionLoop = 0;
        //ajax request
        // console.log("heroPos: ", heroPos);
        $.ajax({
            url: "/position/go?x=" + heroPos.x + "&y=" + heroPos.y,
            // context: document.body
        }).done(function(resp) {
            // $( this ).addClass( "done" );
            console.log("resp: ", resp);
        });
    }



    // console.log('usersArray', usersArray);
    Object.keys(usersArray).forEach(i => {

        // console.log('i', i);
        // console.log('herosPos', herosPos[i]);

        // if(heros[i]) {

            // console.log('herosPos', herosPos[i]);

            // if (typeof herosPos[i] !== 'undefined') {
                // return;
                if (herosPos[i].x + speed < usersArray[i].posx) {
                    herosPos[i].x += speed;
                }
                if (herosPos[i].x - speed > usersArray[i].posx) {
                    herosPos[i].x -= speed;
                }
                if (herosPos[i].y + speed < usersArray[i].posy) {
                    herosPos[i].y += speed;
                }
                if (herosPos[i].y - speed > usersArray[i].posy) {
                    herosPos[i].y -= speed;
                }

                image(heros[usersArray[i].avatar_id], herosPos[i].x - camX, herosPos[i].y - camY, heroSize, heroSize);
            // }

        // }

        if(i >10){
            return;
        }

    });



}

function updateHeroPosition() {

    proposedNewPosition.x = heroPos.x;
    proposedNewPosition.y = heroPos.y;

    if (keyIsDown(65)) { proposedNewPosition.x -= speed; }
    if (keyIsDown(68)) { proposedNewPosition.x += speed; }
    if (keyIsDown(87)) { proposedNewPosition.y -= speed; }
    if (keyIsDown(83)) { proposedNewPosition.y += speed; }


    if (mouseIsPressed) {
        if (millis() - mousePressedTime > moveThreshold) {
            // Mouse has been held down long enough; move hero
            let camX = constrain(proposedNewPosition.x - width / 2, 0, mapWidth - width);
            let camY = constrain(proposedNewPosition.y - height / 2, 0, mapHeight - height);
            let mousePos = createVector(mouseX + camX, mouseY + camY);
            let direction = p5.Vector.sub(mousePos, proposedNewPosition);
            direction.setMag(speed);
            proposedNewPosition.add(direction);
        }
    } else {
        // Reset mousePressedTime when the mouse is released
        mousePressedTime = millis();
    }

    if (!isCollidingWithObjects(proposedNewPosition)) {
        // console.log('proposedNewPosition', proposedNewPosition);
        if(heroPos.x < proposedNewPosition.x){
            heroMoveRight = true;
        } else {
            heroMoveRight = false;
        }
        heroPos.x = proposedNewPosition.x;
        heroPos.y = proposedNewPosition.y;
    }


    // if (mouseIsPressed) {
    //     let camX = constrain(heroPos.x - width / 2, 0, mapWidth - width);
    //     let camY = constrain(heroPos.y - height / 2, 0, mapHeight - height);
    //     let mousePos = createVector(mouseX + camX, mouseY + camY);
    //     let direction = p5.Vector.sub(mousePos, heroPos);
    //     direction.setMag(speed);
    //     heroPos.add(direction);
    // }
}

function mousePressed() {


    console.log('isPopupVisible',isPopupVisible);

    if(isPopupVisible == false) {

        console.log('move');

        let camX = constrain(heroPos.x - width / 2, 0, mapWidth - width);
        let camY = constrain(heroPos.y - height / 2, 0, mapHeight - height);
        let clickPos = createVector(mouseX + camX, mouseY + camY);

        if(currentlyDragging){
        // else {
                // Stop dragging

                //save new position
                console.log('new position', clickPos);
                $.ajax({
                    url: "/position/set?farm_id=" + currentlyDragging + "&x=" + clickPos.x + "&y=" + clickPos.y,
                }).done(function(resp) {
                    console.log("resp: ", resp);

                    farmsObjectPos[currentlyDragging].x = clickPos.x;
                    farmsObjectPos[currentlyDragging].y = clickPos.y;
                    currentlyDragging = null; // Stop dragging


                });



                return;
            // }
        }


        Object.keys(farmsArray).forEach(i => {

            // console.log('farmsArray[i]', farmsArray[i]);
            // if(farmsArray[i].status == 'start' || farmsArray[i].status == 'claim' || i == 18){


            //     let farm = farmsArray[i];
            if (clickPos.x >= farmsObjectPos[i].x - farmsObjectSize[i] / 2 &&
                clickPos.x <= farmsObjectPos[i].x + farmsObjectSize[i] / 2 &&
                clickPos.y >= farmsObjectPos[i].y - farmsObjectSize[i] / 2 &&
                clickPos.y <= farmsObjectPos[i].y + farmsObjectSize[i] / 2) {

                if(edit_mode){

                    if(!currentlyDragging) {
                        currentlyDragging = i; // Store reference to the rectangle being dragged
                        console.log('currentlyDragging', currentlyDragging);
                        //break;
                        return;
                    }


                } else {


                    if (heroPos.dist(farmsObjectPos[i]) <= interactionDistance) {


                        console.log('farmsArray[i]', farmsArray[i]);
                        // resourceArray[farmsArray[i].resorce_id].name
                        //patch for teleport
                        if (farmsArray[i].resource_id == 10) {
                            //window.location.href = '/land/select?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id;
                            land_go_select();
                            //patch fot market sell to buy list
                        } else if (i == 18) {
                            window.location.href = '/service-use/orders?farm_id=18';

                            //start service
                        } else if (farmsArray[i].status == 'start' || farmsArray[i].status == 'take') {

                            //if single service
                            if (farmsArray[i].single_service == true) {
                                // console.log('farmsServiceArray[i]', farmsArray[i]);

                                if (farmsArray[i].status == 'take') {
                                    // take(farmsArray[i].id, farmsArray[i].service_id);
                                    start(farmsArray[i].id, farmsArray[i].service_id);
                                } else {
                                    start(farmsArray[i].id, farmsArray[i].service_id);
                                }


                            } else {
                                // window.location.href = '/service-use/select?farm_id=' + farmsArray[i].id;
                                // claim(farmsArray[i].id,farmsArray[i].service_id);
                                // console.log('farmsServiceArray', farmsServiceArray[i]);
                                select_service(farmsArray[i].id);
                            }

                        } else if (farmsArray[i].status == 'in_use') {

                            // if(farmsArray[i].status == 'in_use') {
                            // Display the remaining cooldown time in seconds
                            let timeInSeconds = farmsArray[i].text; // Round to one decimal place
                            let hours = Math.floor(timeInSeconds / 3600); // 3600 seconds in an hour
                            let minutes = Math.floor((timeInSeconds % 3600) / 60); // Remaining minutes
                            let seconds = timeInSeconds % 60; // Remaining seconds
                            let formattedHours = hours.toString();//.padStart(2, '0');
                            let formattedMinutes = minutes.toString().padStart(2, '0');
                            let formattedSeconds = seconds.toString().padStart(2, '0');
                            timeString = `In use ${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                            // } else {
                            //     timeString = `Reload ${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                            // }
                            document.getElementById('popup-text').textContent = "P: " + timeString;

                            document.getElementById('popup').style.display = 'block';
                            setTimeout(() => {
                                document.getElementById('popup').style.display = 'none';
                            }, 1000);


                            //claim service
                        } else if (farmsArray[i].status == 'claim') {
                            claim(farmsArray[i].id, farmsArray[i].service_id);
                        }


                        // console.log('farmsServiceArray[i]', farmsServiceArray[i]);


                        // let currentTime = millis() / 1000; // Convert milliseconds to seconds
                        // if (currentTime - lastInteractionTime >= cooldownPeriod) {
                        //     lastInteractionTime = currentTime;
                        //     console.log("Interaction occurred on"+i);
                        // Interaction code here (e.g., AJAX request)
                        // } else {
                        //     console.log("Still in cooldown.");
                        // }


                        /*

                        if(i == 18){
                            window.location.href = '/service-use/orders?farm_id=18';
                        } else if (typeof farmsServiceArray[i] !== 'undefined') {
                            if (farmsArray[i].single_service == 1) {

                                if (farmsServiceArray[i].name == "Go") {
                                    //window.location.href = '/land/select?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id;
                                    land_go_select();
                                } else {
                                    // window.location.href = '/service-use/claim?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id;
                                    //claim 1 service that used and start new
                                    console.log(farmsArray[i]);
                                    console.log(farmsServiceArray[i]);
                                    // claim(farmsArray[i].id,farmsArray[i].service_id);
                                }

                            } else {
                                window.location.href = '/service-use/select?farm_id=' + farmsArray[i].id;
                                // claim(farmsArray[i].id,farmsArray[i].service_id);
                            }
                        } else {
                            console.log(farmsArray[i]);
                            console.log(farmsServiceArray[i]);
                            // window.location.href = '/service-use/claim?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id;
                            //ready
                            claim(farmsArray[i].id,farmsArray[i].service_id);
                        }


                         */


                    } else {
                        document.getElementById('popup-text').textContent = "Too far away!";
                        document.getElementById('popup').style.display = 'block';
                        setTimeout(() => {
                            document.getElementById('popup').style.display = 'none';
                        }, 1000);
                    }


                }
            }

            // }
        });
    }
}

function isCollidingWithObjects(proposedPosition) {
    // for (let obj of objects) {
    // return true;

    // console.log('Hx', proposedPosition.x);
    // console.log('Hy', proposedPosition.y);
    // console.log('1x', farmsObjectPos[1].x);
    // console.log('1y', farmsObjectPos[1].y);
    // console.log('==', farmsObjectSize[1]/2);


    // if(proposedPosition.x < farmsObjectPos[1].x &&
    //     proposedPosition.x > farmsObjectPos[1].x - farmsObjectSize[1] &&
    //     proposedPosition.y < farmsObjectPos[1].y &&
    //     proposedPosition.y > farmsObjectPos[1].y - farmsObjectSize[1]
    //
    // ){
    //     return true;
    // }

    let collision = false;
    Object.keys(farmsArray).forEach(i => {
        if(proposedPosition.x < farmsObjectPos[i].x &&
            proposedPosition.x > farmsObjectPos[i].x - farmsObjectSize[i] &&
            proposedPosition.y < farmsObjectPos[i].y &&
            proposedPosition.y > farmsObjectPos[i].y - farmsObjectSize[i]
        ){
            collision = true;
            // return true;
            return;
        }
        if(collision){
            return;
        }
    });
    return collision;
}

function land_go_select() {
    // let html_form = 'Select land [1-5]<form action="/land/go" method="get">';
    // html_form += '<input type="number" name="id" value="1">';
    // html_form += '<input type="submit" value="Go">';
    // html_form += '</form>';
    //ajax call to /api/land/list
    $.ajax({
        url: "/api/land/list",
    }).done(function(resp) {
        // console.log("resp: ", resp);
        isPopupVisible = true;
        let html_form = 'Select land [1-5]<form action="/land/go" method="get">';
        for (let i = 0; i < resp.length; i++) {
            html_form += '- <a href="/land/go?id=' + resp[i].id + '">' + resp[i].name + '</a><br>';
        }
        document.getElementById('popup-text').innerHTML = html_form;
        document.getElementById('popup').style.display = 'block';
    });
}

/*
function take(farm_id,service_id) {
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
        } else {
            //     html += 'Error';
        }

        // if(resp.message){
        //     html += '<br>' + resp.message;
        // }

        if(resp.extra){
            html += '<br>' + resp.extra;
        }

        isPopupVisible = true;
        document.getElementById('popup-text').innerHTML = html;
        document.getElementById('popup').style.display = 'block';

        console.log('serviceArray', serviceArray[service_id]);

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

        setTimeout(() => {
            document.getElementById('popup').style.display = 'none';
            isPopupVisible = false;
        },2000);



        document.getElementById('p-head').textContent = resp.balance;


    });
}
*/
function select_service(farm_id) {

    // console.log('services len', Object.values(farmsServiceArray[farm_id]).length);
    // farmsServiceArray[farm_id].length);
    // farmsServiceArrayLen = Object.values(farmsServiceArray[farm_id]).length;

    $.ajax({
        url: "/api/service-use/select?farm_id=" + farm_id,
    }).done(function(resp) {
        console.log("resp: ", resp);
        console.log("resourceArray[farmsArray[farm_id].resource_id]: ", resourceArray[farmsArray[farm_id].resource_id]);
        // console.log('serviceArray', resp.services);

        let html = '';

        if(!resourceArray[farmsArray[farm_id].resource_id].amountable){
            for (let i = 0; i < resp.services.length; i++) {
                // Object.keys(farmsServiceArray[farm_id]).forEach(i => {
                // console.log('ssssssss', resp.services[i]);
                // console.log('resource_id', farmsArray[farm_id].resource_id);
                html += '- <span onclick = "start(' + farm_id + ',' + resp.services[i].id + ')">' + resp.services[i].name + '</span><br>';
            }
        } else {
            for (let i = 0; i < resp.services.length; i++) {
                // Object.keys(farmsServiceArray[farm_id]).forEach(i => {
                // console.log('ssssssss', resp.services[i]);
                // console.log('resource_id', farmsArray[farm_id].resource_id);
                html += '- <span onclick = "select_amount(' + farm_id + ',' + resp.services[i].id + ')">' + resp.services[i].name + '</span><br>';
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
    html += '(max 100 items)<input type="number" id="amount" name="amount" value="1" min="1" max="100">';
    html += '<button onclick="start_amount(' + farm_id + ',' + service_id + ')">Start</button>';
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


    $.ajax({
        url: "/api/service-use/claim?farm_id=" + farm_id + "&service_id=" + service_id + "&amount=" + $('#amount').val(),
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
        //
        // document.getElementById('p-head').textContent = resp.balance;


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

        if(resp.extra){
            html += '<br>' + resp.extra;
        }

        isPopupVisible = true;

        document.getElementById('popup-text').innerHTML = html;
        document.getElementById('popup').style.display = 'block';

        console.log('serviceArray', serviceArray[service_id]);

        setTimeout(() => {
            document.getElementById('popup').style.display = 'none';
            isPopupVisible = false;
        },2000);

        document.getElementById('balance').textContent = resp.balance;


    });
}
function claim(farm_id,service_id) {
                          // window.location.href = '/service-use/claim?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id;
    $.ajax({
        url: "/api/service-use/claim?farm_id=" + farm_id + "&service_id=" + service_id,
    }).done(function(resp) {
        console.log("resp: ", resp);

        let html = '';
        // if(resp.status == 'success'){
        //     html += 'Success';
        //     delete farmsServiceArray[farm_id];
            //reload page
            // window.location.reload();
        // } else {
        //     html += 'Error';
        // }

        // if(resp.message){
        //     html += '<br>' + resp.message;
        // }

        if(resp.extra){
            html += '<br>' + resp.extra;
        }

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

        document.getElementById('p-head').textContent = resp.balance;


    });
}




