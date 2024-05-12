let mousePressedTime = 0;
const moveThreshold = 200;


let speed = 2;
//patch for vip
if(balanceArray[13] >0){
    speed = 4;
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

let currencyObject = [];
let resourceObject = [];
let resourceObjectHover = [];
let resourceObjectSize = [];
let farmsObjectPos = [];
let servicesObjectCraft = [];
let servicesObjectReady = [];
let servicesObjectReload = [];


let cooldownDuration = [];
let cooldownReady = [];
let startTime;
let isCooldownActive = [];
let timeRemaining = [];

// let updateHeroPositionLoop = 0;
// let updateHeroPositionInterval = 200;


let heros = [];
let herosPos = [];

let edit_mode = false;
let currentlyDraggingFarm = null; // Track the rectangle currently being dragged
let currentlyDraggingResource = null; // Track the rectangle currently being dragged


let joystick = {
    baseX: 100,
    // baseY: windowHeight - 100,
    baseY: 100,
    baseSize: 80,
    stickX: 100,
    // stickY: windowHeight - 100,
    stickY: 100,
    stickSize: 40,
    dragging: false
};

let grid_mode = false;
let cols = grid[0].length;
let rows = grid.length;
let cellSize = mapWidth / cols;

function preload()


{
    // console.log('preload');

    mapBackground = loadImage('storage/'+map);
    // hero = loadImage(avatarsArray[3].img);
    // hero = loadImage('img/fly-sky.gif');

    // console.log(avatarsArray);

    if(avatarsArray[avatar_id].img.length<200){
        hero = loadImage('storage/' + avatarsArray[avatar_id].img);
    } else {
        hero = loadImage(avatarsArray[avatar_id].img);
    }


    // if(avatar_id == 1){
    //     hero = loadImage('img/fly-sky.gif');
    // } else {
    //     hero = loadImage(avatarsArray[avatar_id].img);
    // }
    // posx = posx;
    // posy = posy;


    // farmsArrayLen = Object.values(farmsArray).length;

    Object.keys(currencyArray).forEach(i => {
        if(currencyArray[i].img){
            currencyObject[i] = loadImage('storage/' + currencyArray[i].img);
        } else {
            currencyObject[i] = loadImage('clickableObject.png');
        }
    });

    /*
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
    */

    Object.keys(resourceArray).forEach( i => {

        if (resourceArray[i].img) {
            if(resourceArray[i].img.length<200){
                resourceObject[i] = loadImage('storage/' + resourceArray[i].img);
            } else {
                resourceObject[i] = loadImage(resourceArray[i].img);
            }
        } else {
            resourceObject[i] = loadImage('clickableObject.png');
        }
        if (resourceArray[i].img_hover) {
            if(resourceArray[i].img_hover.length<200){
                resourceObjectHover[i] = loadImage('storage/' + resourceArray[i].img_hover);
            } else {
                resourceObjectHover[i] = loadImage(resourceArray[i].img_hover);
            }
        }
        // else {
        //     farmsObjectHover[i] = loadImage('clickableObject.png');
        // }

        resourceObjectSize[i] = resourceArray[i].size * 10;
    });


    Object.keys(serviceArray).forEach(i => {
        if(serviceArray[i].image_init) {
            // if(serviceArray[i].img.length){
            servicesObjectCraft[i] = loadImage('storage/' + serviceArray[i].image_init);
            // }
        }
        if(serviceArray[i].image_ready) {
            // if(serviceArray[i].img.length){
                servicesObjectReady[i] = loadImage('storage/' + serviceArray[i].image_ready);
            // }
        }

        if(serviceArray[i].image_reload) {
            // if(serviceArray[i].img.length){
            servicesObjectReload[i] = loadImage('storage/' + serviceArray[i].image_reload);
            // }
        }



    });

    // hero = loadImage('img/hero.png');
    Object.keys(avatarsArray).forEach(i => {
        if(avatarsArray[i].img.length<200){
            heros[i] = loadImage('storage/' + avatarsArray[i].img);
        } else {
            heros[i] = loadImage(avatarsArray[i].img);
        }
    });

}

function setup() {



    // console.log('setup');

    // if(windowHeight > 500){
    //     createCanvas(windowWidth, 500);
    // } else {
    let cnv = createCanvas(windowWidth, windowHeight);
    // cnv.drawingContext.setContextAttribute("willReadFrequently", true);
    // cnv.drawingContext.setContextAttribute('willReadFrequently',true);
    // cnv.drawingContext.setAttribute('willReadFrequently', true);


    // ?? cnv.elt.setAttribute('willReadFrequently', true);

    // console.log(cnv.drawingContext.getContextAttributes());

    // let cnv = createCanvas(windowWidth, windowHeight).elt.getContext('2d', { willReadFrequently: true });

    // contextReadFrequently = createCanvas(800, 600).elt.getContext('2d', { willReadFrequently: true });
    // cnv.parent('canvasContainer'); // This div will hold the canvas
    joystick.baseX =joystick.stickX = windowWidth - 100;
    joystick.baseY = joystick.stickY = windowHeight - 100;

    // }
    // cnv.elt.setAttribute('willReadFrequently', true);
    cnv.elt.style.position = 'fixed';
    cnv.elt.style.userSelect = 'none';
    cnv.elt.style.touchAction = 'none';

    // Add event listeners to the canvas element to prevent default behavior on touch start
    cnv.elt.addEventListener('touchstart', function(e) {
        e.preventDefault();
    });

    // Optionally, prevent touch move events from scrolling the page as well
    cnv.elt.addEventListener('touchmove', function(e) {
        e.preventDefault();
    });

    // Button setup
    // let btn_dashboard = createButton('Back');
    // btn_dashboard.position(10, height - 50);
    // btn_dashboard.mousePressed(goBack);
    //
    // let btn_edit = createButton('Edit');
    // btn_edit.position(10, height - 80);
    // btn_edit.mousePressed(


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

    document.getElementById('popup').style.display = 'none';
    // console.log('setup end');




}

function draw() {

    background(220);

    let camX = constrain(heroPos.x - width / 2, 0, mapWidth - width);
    let camY = constrain(heroPos.y - height / 2, 0, mapHeight - height);

    if(mapBackground){
        image(mapBackground, -camX, -camY, mapWidth, mapHeight);
    }

    if(grid_mode) {
        drawGrid(camX, camY);
    }

    // cursor('pointer');

    // console.log('joystick.dragging', joystick.dragging);

    if(hero && !isPopupVisible){
        // console.log('joystick.dragging', joystick.dragging);
        updateHeroPosition();
    }





    let currentTime = millis();
    let timeElapsed = (currentTime - startTime)/1000;

    let isMouseOver = false;
    if(farmsArray) {
        let resourceId = null;
        Object.keys(farmsArray).forEach(i => {

            resourceId = farmsArray[i].resource_id;

            if(currentlyDraggingFarm === i) {

                image(resourceObject[resourceId], mouseX - resourceObjectSize[resourceId] / 2, mouseY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);

            } else {

                if(resourceArray[farmsArray[i].resource_id].type == 'bot' && farmsArray[i].health <= 0){

                } else
                if (
                    mouseX > farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2 &&
                    mouseX < farmsObjectPos[i].x - camX + resourceObjectSize[resourceId] / 2 &&
                    mouseY > farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2 &&
                    mouseY < farmsObjectPos[i].y - camY + resourceObjectSize[resourceId] / 2) {

                    if(farmsArray[i].status == 'start' && servicesObjectCraft[farmsArray[i].service_id]){
                        image(resourceObject[resourceId], farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2, farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);
                    } else
                    if(farmsArray[i].status == 'in_use' && servicesObjectCraft[farmsArray[i].service_id]){
                        image(servicesObjectCraft[farmsArray[i].service_id], farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2, farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);
                    } else
                    if(farmsArray[i].status == 'reload' && servicesObjectReload[farmsArray[i].service_id]) {
                        image(servicesObjectReload[farmsArray[i].service_id], farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2, farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);
                    } else if(servicesObjectReady[farmsArray[i].service_id]) {
                        image(servicesObjectReady[farmsArray[i].service_id], farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2, farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);
                    } else {

                        if (resourceObjectHover[resourceId]) {
                            image(resourceObjectHover[resourceId], farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2, farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);
                        } else {
                            image(resourceObject[resourceId], farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2, farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);
                        }
                    }
                    // if(resourceArray[farmsArray[i].resource_id].type != 'rug') {
                    if(farmsArray[i].service_id) {
                        isMouseOver = true;
                    }
                    // image(farmsObject[i], farmsObjectPos[i].x - camX - farmsObjectSize[i]/2, farmsObjectPos[i].y - camY - farmsObjectSize[i]/2, farmsObjectSize[i], farmsObjectSize[i]);
                    if(resourceArray[farmsArray[i].resource_id].type == 'bot'){
                        drawHealthBar(farmsObjectPos[i].x - camX , farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, farmsArray[i].health, resourceArray[farmsArray[i].resource_id].health);
                    }
                } else {

                    if(farmsArray[i].status == 'start' && servicesObjectCraft[farmsArray[i].service_id]){
                        image(resourceObject[resourceId], farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2, farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);
                    } else
                    if(farmsArray[i].status == 'in_use' && servicesObjectCraft[farmsArray[i].service_id]){
                        image(servicesObjectCraft[farmsArray[i].service_id], farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2, farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);
                    } else
                    if(farmsArray[i].status == 'reload' && servicesObjectReload[farmsArray[i].service_id]) {
                        image(servicesObjectReload[farmsArray[i].service_id], farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2, farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);
                    } else if(servicesObjectReady[farmsArray[i].service_id]) {
                        image(servicesObjectReady[farmsArray[i].service_id], farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2, farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);
                    } else {
                        image(resourceObject[resourceId], farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2, farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);
                    }

                    if(resourceArray[farmsArray[i].resource_id].type == 'bot'){
                        drawHealthBar(farmsObjectPos[i].x - camX , farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, farmsArray[i].health, resourceArray[farmsArray[i].resource_id].health);
                    }
                }

                if(edit_mode) {
                    noFill(); // Ensure the rectangle isn't filled
                    if(resourceArray[farmsArray[i].resource_id].type == 'rug' || resourceArray[farmsArray[i].resource_id].type == 'bot'){
                        stroke(255, 255, 0); // Color the stroke red for visibilitysasadasd
                    } else {
                        stroke(255, 0, 0); // Color the stroke red for visibility
                    }
                    rect(farmsObjectPos[i].x - camX - resourceObjectSize[resourceId] / 2, farmsObjectPos[i].y - camY - resourceObjectSize[resourceId] / 2, resourceObjectSize[resourceId], resourceObjectSize[resourceId]);
                }
            }

            // if(farmsArray[i].status == 'in_use'){

            if (isCooldownActive[i]) {
                // console.log('farmsArray[i]', farmsArray[i]);
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
                        // timeString = `In use ${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                        timeString = `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                    } else {
                        // timeString = `Reload ${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                        timeString = `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
                    }
                    if(farmsArray[i].use_by){
                        timeString = timeString + ' by ' + farmsArray[i].use_by;
                    }
                    fill(255);
                    text(timeString, farmsObjectPos[i].x - camX - resourceObjectSize[resourceId]/2 + 10, farmsObjectPos[i].y - camY + resourceObjectSize[resourceId]/2 -10);
                } else {
                    if(farmsArray[i].status == 'in_use') {
                        if(farmsArray[i].use_by){
                            farmsArray[i].use_by = null;
                            farmsArray[i].status = 'start';
                            farmsArray[i].text = 'Start';
                        } else {
                            farmsArray[i].status = 'claim';
                            farmsArray[i].text = 'Claim';
                        }
                    } else {//reload
                        if(serviceArray[farmsArray[i].service_id].time  > 0){
                            farmsArray[i].status = 'start';
                            farmsArray[i].text = 'Start';
                        } else {
                            farmsArray[i].status = 'take';
                            farmsArray[i].text = 'Take';
                        }
                        if(farmsArray[i].use_by){
                            farmsArray[i].use_by = null;
                        }
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

    if(currentlyDraggingResource){
        image(currencyObject[currentlyDraggingResource], mouseX - 30 / 2, mouseY - 30 / 2, 30, 30);
    }


    heroPos.x = constrain(heroPos.x, 0, mapWidth - heroSize);
    heroPos.y = constrain(heroPos.y, 0, mapHeight - heroSize);
    if(heroMoveRight){
        image(heroFlipped, heroPos.x - camX, heroPos.y - camY, heroSize, heroSize);
    } else {
        image(hero, heroPos.x - camX, heroPos.y - camY, heroSize, heroSize);
    }

    // console,log('heroPos', balanceArray[25]);
    drawHealthBar(heroPos.x - camX + 35, heroPos.y - camY - 10, balanceArray[25], 200);


    if(edit_mode) {
        noFill(); // Ensure the rectangle isn't filled
        stroke(255, 0, 0); // Color the stroke red for visibility
        rect(heroPos.x - camX, heroPos.y - camY, heroSize, heroSize);
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

                if(i == user_id){
                    image(heros[usersArray[i].avatar_id], herosPos[i].x - camX, herosPos[i].y - camY + 30, 40, 40);
                } else {
                    image(heros[usersArray[i].avatar_id], herosPos[i].x - camX, herosPos[i].y - camY, heroSize, heroSize);
                }

            }

            // }

            if (i > 10) {
                return;
            }

        });
    }

    if(windowWidth < 1000) {
        drawJoystick();
    }

    // Check for mouse over any rectangle
    if (isMouseOver) {
        cursor('pointer');
    // console.log('cursor');
    } else {
        cursor('default');
    }




    // console.log('draw end');
}

function updateHeroPosition() {

    proposedNewPosition.x = heroPos.x;
    proposedNewPosition.y = heroPos.y;

    if (keyIsDown(65)) { proposedNewPosition.x -= speed; }
    if (keyIsDown(68)) { proposedNewPosition.x += speed; }
    if (keyIsDown(87)) { proposedNewPosition.y -= speed; }
    if (keyIsDown(83)) { proposedNewPosition.y += speed; }

    // if (mouseIsPressed && !joystick.dragging) {
    if(!joystick.dragging) {
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
    } else {
            let dx = joystick.stickX - joystick.baseX;
            let dy = joystick.stickY - joystick.baseY;
            let angle = atan2(dy, dx);
            proposedNewPosition.x += cos(angle) * speed;
            proposedNewPosition.y += sin(angle) * speed;
    }

    if(heroPos.x != proposedNewPosition.x || heroPos.y != proposedNewPosition.y ){

        gx= Math.floor((proposedNewPosition.x + heroSize /2 ) / cellSize );
        gy = Math.floor((proposedNewPosition.y + heroSize /2)  / cellSize);

        if (
            gx >= 0 && gx < cols && gy >= 0 && gy < rows &&
            grid[gy][gx] == 0) {
            // console.log('ok to move grid[newY][newX]');
            // console.log('proposedNewPosition ok', gx,gy);
            if (!isCollidingWithObjects(proposedNewPosition)) {

                if(heroPos.x < proposedNewPosition.x){
                    heroMoveRight = true;
                }else if(heroPos.x > proposedNewPosition.x){
                    heroMoveRight = false;
                }
                heroPos.x = proposedNewPosition.x;
                heroPos.y = proposedNewPosition.y;
            }
        }

        // else {
        //     console.log('proposedNewPosition not ok', gx,gy);
        //
        // }



    }
    // else {
    //     console.log('hero not move');
    // }



}

function isCollidingWithObjects(proposedPosition) {
    let collision = false;
    Object.keys(farmsArray).forEach(i => {
        // if(i == 40){
        //     console.log(camX,' pur-x: ', farmsObjectPos[i].x - resourceObjectSize[resourceId]/2 , ' < ' , proposedPosition.x , ' < ' , farmsObjectPos[i].x + resourceObjectSize[resourceId]/2);
        // }
        resourceId = farmsArray[i].resource_id;
        divi = resourceObjectSize[resourceId]/22 ;//10; //50;
        divi = divi*divi;
        if( resourceArray[resourceId].type != 'rug' &&
            resourceArray[resourceId].type != 'bot' &&

            // proposedPosition.x > farmsObjectPos[i].x - resourceObjectSize[resourceId]/2  && //+ 20  &&
            farmsObjectPos[i].x - resourceObjectSize[resourceId] + divi < proposedPosition.x  &&
            proposedPosition.x  < farmsObjectPos[i].x + divi && // + 20 &&

            // proposedPosition.y > farmsObjectPos[i].y - resourceObjectSize[resourceId] && // +20 &&
            farmsObjectPos[i].y - resourceObjectSize[resourceId] + divi < proposedPosition.y && // +20 &&
            proposedPosition.y < farmsObjectPos[i].y + divi //+ 20

        ){
            collision = true;

            // console.log('resource_id and type', farmsArray[i].resource_id, resourceArray[resourceId].type);
            if(resourceArray[resourceId].type == 'portal'){
                console.log('portal');
                isPopupVisible = true;
                document.getElementById('popup-text').innerHTML = 'Loading ...';
                document.getElementById('popup').style.display = 'block';
                window.location.href = '/land/portal?resource_id=' + resourceId;
                return;
            }



            //return;
        }
        if(collision){
            return;
        }
    });
    return collision;
}

function mousePressed() {


    // console.log('press');

    if(isPopupVisible == false) {

        // console.log('move');

        let camX = constrain(heroPos.x - width / 2, 0, mapWidth - width);
        let camY = constrain(heroPos.y - height / 2, 0, mapHeight - height);
        let clickPos = createVector(mouseX + camX, mouseY + camY);


        if(grid_mode) {
            let gx = Math.floor(clickPos.x / cellSize);
            let gy = Math.floor(clickPos.y / cellSize);
            // if (gx >= 0 && gx < cols && gy >= 0 && gy < rows) {
            //     grid[gy][gx] = grid[gy][gx] == 0 ? 1 : 0;
            // }
            console.log('grid[gy][gx]', grid[gy][gx]);
            if (grid[gy][gx] == 0) {
                console.log('change to busy (1) ');
                grid[gy][gx] = 1;
            } else {
                console.log('change to free (0) ');
                grid[gy][gx] = 0;
            }

        }else if(edit_mode || currentlyDraggingResource){

            let freePos = true;
            let resourceId = null;
            Object.keys(farmsArray).forEach(i => {
                resourceId = farmsArray[i].resource_id;

                if (clickPos.x >= farmsObjectPos[i].x - resourceObjectSize[resourceId] / 2 &&
                    clickPos.x <= farmsObjectPos[i].x + resourceObjectSize[resourceId] / 2 &&
                    clickPos.y >= farmsObjectPos[i].y - resourceObjectSize[resourceId] / 2 &&
                    clickPos.y <= farmsObjectPos[i].y + resourceObjectSize[resourceId] / 2) {
                    freePos = false;
                    if (!currentlyDraggingFarm && !currentlyDraggingResource) {
                        currentlyDraggingFarm = i; // Store reference to the rectangle being dragged
                        // console.log('currentlyDragging', currentlyDragging);

                        // return;
                    }
                    // else {
                    //     freePos = false;
                    //     // return;
                    // }

                    //if resource have service id start service
                    if(currentlyDraggingResource && currencyArray[currentlyDraggingResource].service_id){
                        console.log('claim',currencyArray[currentlyDraggingResource].service_id);
                        start(i, currencyArray[currentlyDraggingResource].service_id);
                    }
                }
            });

            if(currentlyDraggingFarm){
                if(freePos) {
                    console.log('currentlyDraggingFarm set farm', currentlyDraggingFarm);
                    //set farm
                    move_farm(clickPos);
                } else {
                    console.log('move_farm - pos not free');
                }
            }

            //if dragging resource and the resource has resource id
            if(currentlyDraggingResource ){
                if(currencyArray[currentlyDraggingResource].resource_id) {
                    if (freePos) {
                        console.log('currentlyDraggingResource set farm', currentlyDraggingResource);
                        //set farm
                        set_farm(clickPos);
                    } else {
                        console.log('set_farm - pos not free');
                    }
                }
                else {
                    console.log('set_farm - no resource id');
                }

                //if click on hero posision
                // console.log(currencyArray[currentlyDraggingResource].service_id);
                // console.log('clickPosX', heroPos.x - heroSize / 2 , clickPos.x, heroPos.x + heroSize / 2);
                // console.log('clickPosX', heroPos.x , clickPos.x, heroPos.x + heroSize);
                // console.log('clickPosY', heroPos.y - heroSize / 2 , clickPos.y, heroPos.y + heroSize / 2);
                console.log('clickPosY', heroPos.y, clickPos.y, heroPos.y + heroSize);

                if(currencyArray[currentlyDraggingResource].service_id &&
                    // clickPos.x >= heroPos.x - heroSize / 2 &&
                    // clickPos.x <= heroPos.x + heroSize / 2 &&
                    clickPos.x >= heroPos.x &&
                    clickPos.x <= heroPos.x + heroSize &&
                    clickPos.y >= heroPos.y &&
                    clickPos.y <= heroPos.y + heroSize) {

                    console.log('click on hero(9) with service id', currencyArray[currentlyDraggingResource].service_id);
                    //if dragging resource and the resource has service id
                    // console.log('claim', currencyArray[currentlyDraggingResource].service_id);
                    start(9, currencyArray[currentlyDraggingResource].service_id);
                }

            }



        } else {
            let resourceId = null;
            Object.keys(farmsArray).forEach(i => {
                resourceId = farmsArray[i].resource_id;
                // console.log('farmsArray[i]', farmsArray[i]);
                // if(farmsArray[i].status == 'start' || farmsArray[i].status == 'claim' || i == 18){


                //     let farm = farmsArray[i];
                if ((typeof farmsArray[i].service_id !== 'undefined' || edit_mode) &&
                    clickPos.x >= farmsObjectPos[i].x - resourceObjectSize[resourceId] / 2 &&
                    clickPos.x <= farmsObjectPos[i].x + resourceObjectSize[resourceId] / 2 &&
                    clickPos.y >= farmsObjectPos[i].y - resourceObjectSize[resourceId] / 2 &&
                    clickPos.y <= farmsObjectPos[i].y + resourceObjectSize[resourceId] / 2) {

                    if (heroPos.dist(farmsObjectPos[i]) <= interactionDistance) {

                        console.log('farmsArray[i]', farmsArray[i]);
                        if (currentlyDraggingResource) {
                            // freePos = false;
                            //start service
                            start_with(farmsArray[i].id, currentlyDraggingResource);
                        }
                            // resourceArray[farmsArray[i].resorce_id].name
                        //patch for teleport
                        else if (farmsArray[i].resource_id == 10) {
                            //window.location.href = '/land/select?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id;
                            land_go_select();
                            //patch fot market sell to buy list
                        }
                        // else if (i == 18) {
                        //     select_service(farmsArray[i].id);
                        //     // window.location.href = '/service-use/orders?farm_id=18';
                        //
                        //     //start service
                        // }
                        else if (farmsArray[i].status == 'start' || farmsArray[i].status == 'take') {

                            //if single service
                            if (farmsArray[i].single_service == true) {
                                // console.log('farmsServiceArray[i]', farmsArray[i]);

                                // if (farmsArray[i].status == 'take') {
                                //     // take(farmsArray[i].id, farmsArray[i].service_id);
                                //     start(farmsArray[i].id, farmsArray[i].service_id);
                                // } else {
                                //if user attack resource and not in use
                                if(resourceArray[farmsArray[i].resource_id].type == 'bot'){

                                    if(farmsArray[i].health > 0){
                                        attack(i,farmsArray[i].service_id);
                                    }
                                    // return;

                                } else {
                                    start(farmsArray[i].id, farmsArray[i].service_id);
                                }


                            } else {
                                // window.location.href = '/service-use/select?farm_id=' + farmsArray[i].id;
                                // claim(farmsArray[i].id,farmsArray[i].service_id);
                                // console.log('farmsServiceArray', farmsServiceArray[i]);

                                if(resourceArray[farmsArray[i].resource_id].type == 'bot'){

                                    if(farmsArray[i].health > 0){
                                        select_attack(i);
                                    }
                                    // return;

                                } else {
                                    select_service(farmsArray[i].id);
                                }
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
                            // claim(farmsArray[i].id, farmsArray[i].service_id);
                            start(farmsArray[i].id, farmsArray[i].service_id);
                        }


                    } else {
                        document.getElementById('popup-text').textContent = "Too far away!";
                        document.getElementById('popup').style.display = 'block';
                        setTimeout(() => {
                            document.getElementById('popup').style.display = 'none';
                        }, 1000);
                    }


                    // }
                }

                // }
            });

        }



    }
    //return false;
}

function touchStarted() {
    let d = dist(mouseX, mouseY, joystick.baseX, joystick.baseY);
    if (d < joystick.baseSize / 2) {
        joystick.dragging = true;
        // console.log('joystick.dragging', joystick.dragging);
        return false;
    }
    if(!isPopupVisible){
        mousePressed();
    }
    // return false; // this break popups clicks
}


function touchMoved() {
    if (joystick.dragging) {
        let dx = mouseX - joystick.baseX;
        let dy = mouseY - joystick.baseY;
        let angle = atan2(dy, dx);
        let distance = min(dist(0, 0, dx, dy), joystick.baseSize / 2);
        joystick.stickX = joystick.baseX + cos(angle) * distance;
        joystick.stickY = joystick.baseY + sin(angle) * distance;
        // hero.x += cos(angle) * 2;
        // hero.y += sin(angle) * 2;

        // console.log('heroPos.x', heroPos.x);
        // console.log('heroPos.y', heroPos.y);

        // proposedNewPosition.x = heroPos.x + cos(angle) * 4;
        // proposedNewPosition.y = heroPos.y + sin(angle) * 4;

        // let camX = constrain(proposedNewPosition.x - width / 2, 0, mapWidth - width);
        // let camY = constrain(proposedNewPosition.y - height / 2, 0, mapHeight - height);
        // let mousePos = createVector(mouseX + camX, mouseY + camY);
        // let direction = p5.Vector.sub(mousePos, proposedNewPosition);
        // direction.setMag(speed);
        // proposedNewPosition.add(direction);

        // if (!isCollidingWithObjects(proposedNewPosition)) {
        //     // console.log('proposedNewPosition', proposedNewPosition);
        //     if(heroPos.x < proposedNewPosition.x){
        //         heroMoveRight = true;
        //     }else if(heroPos.x > proposedNewPosition.x){
        //         heroMoveRight = false;
        //     }
        //     heroPos.x = proposedNewPosition.x;
        //     heroPos.y = proposedNewPosition.y;
        // }

    }
    return false;
}

function touchEnded() {
    joystick.dragging = false;
    joystick.stickX = joystick.baseX;
    joystick.stickY = joystick.baseY;
    // console.log('joystick.dragging', joystick.dragging);
    // return false; // this break popups clicks
}

function drawJoystick() {
    // fill(80);
    fill('rgba(100, 100, 255, 0.5)');
    circle(joystick.baseX, joystick.baseY, joystick.baseSize);
    // fill(160);
    fill('rgba(255, 100, 100, 0.5)');
    circle(joystick.stickX, joystick.stickY, joystick.stickSize);
}


function drawHealthBar(x, y, health, maxHealth) {
    let barWidth = 50;
    let barHeight = 10;
    let healthRatio = health / maxHealth;

    // Draw health bar background
    fill(255);
    rect(x - 25, y, barWidth, barHeight);

    // Draw health bar foreground
    fill(255, 0, 0);
    rect(x - 25, y, barWidth * healthRatio, barHeight);
}


function drawGrid(camX, camY) {
    for (let i = 0; i < rows; i++) {
        for (let j = 0; j < cols; j++) {
            if (grid[i][j] == 1) {
                fill(255, 0, 0);
            } else {
                // fill(0,255,0,0.6);
                noFill();
            }
            stroke(0);
            rect(j * cellSize - camX , i * cellSize - camY, cellSize, cellSize);
        }
    }
}
