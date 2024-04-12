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
    // hero = loadImage(avatarsArray[3].img);
    hero = loadImage('img/fly-sky.gif');
    if(avatar_id == 1){
        hero = loadImage('img/fly-sky.gif');
    } else {
        hero = loadImage(avatarsArray[avatar_id].img);
    }
    posx = posx;
    posy = posy;


    // farmsArrayLen = Object.values(farmsArray).length;

    Object.keys(farmsArray).forEach( i => {

        if (resourceArray[farmsArray[i].resource_id].img) {
            if(resourceArray[farmsArray[i].resource_id].img.length<200){
                farmsObject[i] = loadImage('storage/' + resourceArray[farmsArray[i].resource_id].img);
            } else {
                farmsObject[i] = loadImage(resourceArray[farmsArray[i].resource_id].img);
            }
        } else {
            farmsObject[i] = loadImage('clickableObject.png');
        }
        if (resourceArray[farmsArray[i].resource_id].img_hover) {
            if(resourceArray[farmsArray[i].resource_id].img_hover.length<200){
                farmsObjectHover[i] = loadImage('storage/' + resourceArray[farmsArray[i].resource_id].img_hover);
            } else {
                farmsObjectHover[i] = loadImage(resourceArray[farmsArray[i].resource_id].img_hover);
            }
        }
        // else {
        //     farmsObjectHover[i] = loadImage('clickableObject.png');
        // }

        farmsObjectSize[i] = resourceArray[farmsArray[i].resource_id].size * 10;
    });

    // hero = loadImage('img/hero.png');


    Object.keys(avatarsArray).forEach(i => {

        if(i == 1){
            heros[i] = loadImage('img/fly-sky.gif');
        } else {
            heros[i] = loadImage(avatarsArray[i].img);
        }


    });



}

function setup() {

    if(windowHeight > 500){
        createCanvas(windowWidth, 500);
    } else {
        createCanvas(windowWidth, windowHeight);
    }

    // createCanvas(windowWidth, 500);
    heroFlipped = createGraphics(hero.width, hero.height);
    heroFlipped.scale(-1, 1); // Flip the image horizontally
    heroFlipped.image(hero, -hero.width, 0);

    heroPos = createVector(posx, posy);
    proposedNewPosition = createVector(posx, posy);

    startTime = millis();

    if(farmsArray){
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

                if (farmsObjectHover[i] &&
                    mouseX > farmsObjectPos[i].x - camX - farmsObjectSize[i] / 2 &&
                    mouseX < farmsObjectPos[i].x - camX + farmsObjectSize[i] / 2 &&
                    mouseY > farmsObjectPos[i].y - camY - farmsObjectSize[i] / 2 &&
                    mouseY < farmsObjectPos[i].y - camY + farmsObjectSize[i] / 2) {
                    // fill('red');

                    // farmsObjectHover
                    image(farmsObjectHover[i], farmsObjectPos[i].x - camX - farmsObjectSize[i] / 2, farmsObjectPos[i].y - camY - farmsObjectSize[i] / 2, farmsObjectSize[i], farmsObjectSize[i]);
                    // image(farmsObject[i], farmsObjectPos[i].x - camX - farmsObjectSize[i]/2, farmsObjectPos[i].y - camY - farmsObjectSize[i]/2, farmsObjectSize[i], farmsObjectSize[i]);

                } else {
                    // fill('black');
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
        $.ajax({
            url: "/position/go?x=" + heroPos.x + "&y=" + heroPos.y,
            // context: document.body
        });
        // .done(function(resp) {
        //     // $( this ).addClass( "done" );
        //     console.log("resp: ", resp);
        // });
    }



    // console.log('usersArray', usersArray);
    if(usersArrayLen > 0) {
        Object.keys(usersArray).forEach(i => {

            // console.log('i', i);
            // console.log('herosPos', herosPos[i]);

            // if(heros[i]) {

            // console.log('herosPos', herosPos[i]);

            if (typeof herosPos[i] !== 'undefined') {
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
            }

            // }

            if (i > 10) {
                return;
            }

        });
    }


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
        }else if(heroPos.x > proposedNewPosition.x){
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
                    // console.log("resp: ", resp);

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
            if ((typeof farmsArray[i].service_id !== 'undefined' || edit_mode) &&
                clickPos.x >= farmsObjectPos[i].x - farmsObjectSize[i] / 2 &&
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
