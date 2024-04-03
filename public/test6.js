let hero;
let mapBackground;
let heroPos;
let speed = 2;
let heroSize = 50;
let mapWidth = 900;
let mapHeight = 700;
// let clickableObjectPos;
// let clickableObject;
// let clickableObjectSize = 70;
// let interactionDistance = 100;
// let lastInteractionTime = -10; // Initialize to allow immediate interaction
// let cooldownPeriod = 10; // Cooldown period in seconds

let farmsObjectPos = [];
let farmsObject = [];
let farmsObjectSize  = [];
let farmsObjectStatus = [];

let cooldownDuration = [];
let startTime;
let isCooldownActive = [];
let timeRemaining = [];


// let farmObjectPos;
// let farmObject;


let updateHeroPositionLoop = 0;
let updateHeroPositionInterval = 200;

// farmsArray = JSON.parse(farmsArray);

farmsArrayLen = Object.values(farmsArray).length;
// farmsServiceArray = Object.values(farmsServiceArray);

console.log('farmsArray', farmsArray);
console.log('farmsServiceArray', farmsServiceArray);

// let heroBot = {
//     x: 200,
//     y: 100,
//     size: 30,
//     speed: 1 // How fast the hero moves in any direction
// };


function preload() {
    hero = loadImage('hero.png');
    mapBackground = loadImage('img/map/map-'+land_id+'.jpg');
    // clickableObject = loadImage('clickableObject.png');
    // console.log('farmsArray', farmsArray);
    //size
    // console.log('farmsArrayLen', typeof (farmsArray));
    // console.log();

    Object.keys(farmsArray).forEach(i => {
        console.log(i, farmsArray[i]);
        if(farmsArray[i].resource_id == 1){
            farmsObject[i] = loadImage('img/res/tree.png');
        }else if(farmsArray[i].resource_id == 2){
            farmsObject[i] = loadImage('img/res/mount.png');
        }else if(farmsArray[i].resource_id == 3){
            farmsObject[i] = loadImage('img/res/market.png');
        }else if(farmsArray[i].resource_id == 4){
            farmsObject[i] = loadImage('img/res/soil.png');
        } else {
            farmsObject[i] = loadImage('clickableObject.png');
        }
        farmsObjectSize[i] = farmsArray[i].size * 10;
    });


    // for(let i = 0; i < farmsArrayLen; i++){
    //     if(farmsArray[i].resource_id == 1){
    //         farmsObject[i] = loadImage('img/res/tree.png');
    //     }else if(farmsArray[i].resource_id == 2){
    //         farmsObject[i] = loadImage('img/res/mount.png');
    //     }else if(farmsArray[i].resource_id == 3){
    //         farmsObject[i] = loadImage('img/res/market.png');
    //     }else if(farmsArray[i].resource_id == 4){
    //         farmsObject[i] = loadImage('img/res/soil.png');
    //     } else {
    //         farmsObject[i] = loadImage('clickableObject.png');
    //     }
    //     farmsObjectSize[i] = farmsArray[i].size * 10;
    // }

    // farmObject = loadImage('clickableObject.png');

}

function setup() {
    createCanvas(700, 400);
    // heroPos = createVector(mapWidth / 2, mapHeight / 2);
    heroPos = createVector(posx, posy);
    // clickableObjectPos = createVector(200, 200);

    // farmObjectPos = createVector(200, 200);

    console.log(farmsArray[1].posx, farmsArray[1].posy);
    console.log(farmsArray.length);

    // farmsArray.forEach(async (farm) => {
    //     console.log('farm', farm);
    // })

    startTime = millis();
    // isCooldownActive = true;

    // for(let i = 0; i < farmsArray.length; i++){
    Object.keys(farmsArray).forEach(i => {
        farmsObjectPos[i] = createVector(farmsArray[i].posx, farmsArray[i].posy);
        if(farmsArray[i].status == 'start' || farmsArray[i].status == 'claim'){

            if (typeof farmsServiceArray[i] !== 'undefined'){
                console.log(farmsServiceArray[i]);
                farmsObjectStatus[i] = createA('/service-use/select?farm_id=' + farmsArray[i].id , 'Select');

            // if(farmsArray[i].status == 'start' && farmServiceArray[i].length > 1){
                // for(let j = 0; j < farmServiceArray.length; j++){
                //
                // }
            } else {
                // farmsObjectStatus[i] = createA('/service-use/claim?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id, farmsArray[i].status, '_blank');
                farmsObjectStatus[i] = createA('/service-use/claim?farm_id=' + farmsArray[i].id + '&service_id=' + farmsArray[i].service_id, farmsArray[i].text);

            }
        } else {
            cooldownDuration[i] = farmsArray[i].text
            isCooldownActive[i] = true;
        }

    });
    // }

}

function draw() {
    background(220);

    let camX = constrain(heroPos.x - width / 2, 0, mapWidth - width);
    let camY = constrain(heroPos.y - height / 2, 0, mapHeight - height);

    image(mapBackground, -camX, -camY, mapWidth, mapHeight);

    updateHeroPosition();

    fill('skyblue'); // Set the fill color for the rectangle
    textSize(20); // Set the text size
    // textAlign(CENTER, CENTER); // Align the text to be centered


    // image(clickableObject, clickableObjectPos.x - camX - clickableObjectSize/2, clickableObjectPos.y - camY - clickableObjectSize/2, clickableObjectSize, clickableObjectSize);
    // image(farmObject, farmObjectPos.x - camX - clickableObjectSize/2, farmObjectPos.y - camY - clickableObjectSize/2, clickableObjectSize, clickableObjectSize);

    let currentTime = millis();
    let timeElapsed = (currentTime - startTime)/1000;




    // for(let i = 0; i < farmsArray.length; i++){
    Object.keys(farmsArray).forEach(i => {
    //     let farm = farmsArray[i];
        image(farmsObject[i], farmsObjectPos[i].x - camX - farmsObjectSize[i]/2, farmsObjectPos[i].y - camY - farmsObjectSize[i]/2, farmsObjectSize[i], farmsObjectSize[i]);
        // return;
        // let textContent = farmsArray[i].status;

        // Create a clickable link
        if(farmsArray[i].status == 'start' || farmsArray[i].status == 'claim'){
            farmsObjectStatus[i].position(farmsObjectPos[i].x - camX + 400, farmsObjectPos[i].y - camY + 200);
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
                    text(timeString, farmsObjectPos[i].x - camX -30, farmsObjectPos[i].y - camY + 30 );
                } else {
                    // Cooldown complete
                    text("Cooldown Complete!", farmsObjectPos[i].x - camX, farmsObjectPos[i].y - camY );
                    isCooldownActive[i] = false; // Stop the cooldown
                }
            } else {
                // text("Click to Start Cooldown", width / 2, height / 2);
            }
            // text(farmsArray[i].text, farmsObjectPos[i].x - camX +20, farmsObjectPos[i].y - camY +20);



        }
        // link.style('font-size', '16px');

    });//



    heroPos.x = constrain(heroPos.x, 0, mapWidth - heroSize);
    heroPos.y = constrain(heroPos.y, 0, mapHeight - heroSize);
    image(hero, heroPos.x - camX, heroPos.y - camY, heroSize, heroSize);

    //update hero position
    updateHeroPositionLoop++;
    if (updateHeroPositionLoop >= updateHeroPositionInterval) {
        updateHeroPositionLoop = 0;
        //ajax request
        console.log("heroPos: ", heroPos);
        $.ajax({
            url: "/position/go?x=" + heroPos.x + "&y=" + heroPos.y,
            // context: document.body
        }).done(function(resp) {
            // $( this ).addClass( "done" );
            console.log("resp: ", resp);
        });
    }


//     // Update the hero's position
//     heroBot.x += random(-heroBot.speed, heroBot.speed);
//     heroBot.y += random(-heroBot.speed, heroBot.speed);
//
//     heroBot.x = constrain(heroBot.x, 0 + heroBot.size / 2, width - heroBot.size / 2);
//     heroBot.y = constrain(heroBot.y, 0 + heroBot.size / 2, height - heroBot.size / 2);
//
// // Draw the hero
//     fill(255, 0, 0); // Red color
//     ellipse(heroBot.x, heroBot.y, heroBot.size);
}

function updateHeroPosition() {
    if (keyIsDown(65)) { heroPos.x -= speed; }
    if (keyIsDown(68)) { heroPos.x += speed; }
    if (keyIsDown(87)) { heroPos.y -= speed; }
    if (keyIsDown(83)) { heroPos.y += speed; }

    if (mouseIsPressed) {
        let camX = constrain(heroPos.x - width / 2, 0, mapWidth - width);
        let camY = constrain(heroPos.y - height / 2, 0, mapHeight - height);
        let mousePos = createVector(mouseX + camX, mouseY + camY);
        let direction = p5.Vector.sub(mousePos, heroPos);
        direction.setMag(speed);
        heroPos.add(direction);
    }
}

function mousePressed() {
    let camX = constrain(heroPos.x - width / 2, 0, mapWidth - width);
    let camY = constrain(heroPos.y - height / 2, 0, mapHeight - height);
    let clickPos = createVector(mouseX + camX, mouseY + camY);

    // if (!isCooldownActive) {
    //     startTime = millis();
    //     isCooldownActive = true;
    // }

    // if (clickPos.dist(clickableObjectPos) <= clickableObjectSize / 2) {
    //     if (heroPos.dist(clickableObjectPos) <= interactionDistance) {
    //         // Send AJAX request here
    //
    //         console.log("AJAX request sent.");
    //         document.getElementById('popup').style.display = 'block';
    //
    //         // Example: fetch('your_endpoint', { method: 'POST', body: JSON.stringify({ action: 'interact' }) })
    //     } else {
    //         document.getElementById('popup-text').textContent = "Too far away!";
    //         document.getElementById('popup').style.display = 'block';
    //         setTimeout(() => {
    //             document.getElementById('popup').style.display = 'none';
    //         },1000);
    //     }
    // }
}



/*
function draw() {
    background(220);
    let camX = constrain(heroPos.x - width / 2, 0, mapWidth - width);
    let camY = constrain(heroPos.y - height / 2, 0, mapHeight - height);
    image(mapBackground, -camX, -camY, mapWidth, mapHeight);
    updateHeroPosition();
    // console.log("heroPos: ", heroPos);

    heroPos.x = constrain(heroPos.x, 0, mapWidth - heroSize);
    heroPos.y = constrain(heroPos.y, 0, mapHeight - heroSize);


    image(hero, heroPos.x - camX, heroPos.y - camY, heroSize, heroSize);
    image(clickableObject, clickableObjectPos.x - camX - clickableObjectSize / 2, clickableObjectPos.y - camY - clickableObjectSize / 2, clickableObjectSize, clickableObjectSize);

    //update hero position
    updateHeroPositionLoop++;
    if (updateHeroPositionLoop >= updateHeroPositionInterval) {
        updateHeroPositionLoop = 0;
        //ajax request
        console.log("heroPos: ", heroPos);
        $.ajax({
            url: "/position/go?x=" + heroPos.x + "&y=" + heroPos.y,
            // context: document.body
        }).done(function(resp) {
            // $( this ).addClass( "done" );
            console.log("resp: ", resp);
        });

    }

    // Handle cooldown display
    handleCooldownDisplay(camX, camY);
}

function updateHeroPosition() {
    if (keyIsDown(65)) { heroPos.x -= speed; }
    if (keyIsDown(68)) { heroPos.x += speed; }
    if (keyIsDown(87)) { heroPos.y -= speed; }
    if (keyIsDown(83)) { heroPos.y += speed; }

    if (mouseIsPressed) {
        let camX = constrain(heroPos.x - width / 2, 0, mapWidth - width);
        let camY = constrain(heroPos.y - height / 2, 0, mapHeight - height);
        let mousePos = createVector(mouseX + camX, mouseY + camY);
        let direction = p5.Vector.sub(mousePos, heroPos);
        direction.setMag(speed);
        heroPos.add(direction);
    }

}




function mousePressed() {
    let camX = constrain(heroPos.x - width / 2, 0, mapWidth - width);
    let camY = constrain(heroPos.y - height / 2, 0, mapHeight - height);
    let mousePos = createVector(mouseX + camX, mouseY + camY);

    if (mousePos.x >= clickableObjectPos.x - clickableObjectSize / 2 &&
        mousePos.x <= clickableObjectPos.x + clickableObjectSize / 2 &&
        mousePos.y >= clickableObjectPos.y - clickableObjectSize / 2 &&
        mousePos.y <= clickableObjectPos.y + clickableObjectSize / 2) {
        if (heroPos.dist(clickableObjectPos) <= interactionDistance) {
            let currentTime = millis() / 1000; // Convert milliseconds to seconds
            if (currentTime - lastInteractionTime >= cooldownPeriod) {
                lastInteractionTime = currentTime;
                console.log("Interaction occurred.");
                // Interaction code here (e.g., AJAX request)
            } else {
                console.log("Still in cooldown.");
            }
        } else {
            alert("Too far away!");
        }
    }
}

function handleCooldownDisplay(camX, camY) {
    let currentTime = millis() / 1000;
    if (currentTime - lastInteractionTime < cooldownPeriod) {
        // Calculate remaining cooldown time
        let remainingTime = ceil(cooldownPeriod - (currentTime - lastInteractionTime));
        fill(0);
        textAlign(CENTER, CENTER);
        // Display countdown timer near the object
        text(remainingTime.toString(), clickableObjectPos.x - camX, clickableObjectPos.y - camY - 20);
    }
}
*/
