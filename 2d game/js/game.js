game_state = 0;
intro_wait = false;

//load the intro graphics
intro = new Image();
intro.src = "img/intro.jpg";

//load the character select graphics
man_select = new Image();
man_select.src = "img/man_select.png";
woman_select = new Image();
woman_select.src = "img/woman_select.png";

//load sprites
man_sprite = new Image();
man_sprite.src = "img/man_1.png";
woman_sprite = new Image();
woman_sprite.src = "img/woman_1.png";

//load tiles
tiles = [];
tiles.push(new Image());
tiles[0].src = "img/tile_1.png";
tiles.push(new Image());
tiles[1].src = "img/tile_2.png";
tiles.push(new Image());
tiles[2].src = "img/tile_3.png";

//initialize mousePos
mousePos = { x: 0, y: 0 };

//initialize the map
map = [];

//generate a map
function generateMap() {
    map = []
    for (var i = 0; i < 100; i++) {
        for (var j = 0; j < 100; j++) {
            tileType=Math.floor(Math.random()*3);
            map.push(tileType);
        }
    }
}
generateMap();

//draw the screen
function drawScreen() {

    //clear the background with black
    ctx.fillStyle = "black";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    if (game_state == 0) {

        //draw the intro graphics
        if (intro.loaded) ctx.drawImage(intro, 0, 0, canvas.width, canvas.height);
        if (!intro_wait) {
            setTimeout(function () { game_state = 1; drawScreen(); }, 2000);
            intro_wait = true;
        }
    }

    else if (game_state == 2) {

        ctx.filter='none';

        //draw the map
        for (var i = -10; i < 10; i++) {
            for (var j = -10; j < 10; j++) {
                tileType = map[(i+10)*10+j+10];
                ctx.drawImage(tiles[tileType], canvas.width/2 + i * 64 - 32, canvas.height/2 + j * 64 - 32, 64, 64);
            }
        }

        //draw the sprite
        if (character_select == 1 && man_sprite.loaded) ctx.drawImage(man_sprite, (canvas.width-64)/2, (canvas.height-64)/2, 64, 64);
        else if (character_select == 2 && woman_sprite.loaded) ctx.drawImage(woman_sprite, (canvas.width-64)/2, (canvas.height-64)/2, 64, 64);
    }

    else if (game_state == 1) {

        if (canvas.width > canvas.height) {
            x = canvas.height * .5;
            y = canvas.height * 1;
            xm = (canvas.width - (x * 2)) * .5;
            ym = 0;
        }
        else {
            x = canvas.width * .5;
            y = canvas.width * 1;
            ym = (canvas.height - (x * 2)) * .5;
        }

        //check the mouse position
        if (mousePos.x > xm && mousePos.x < x + xm && mousePos.y > ym && mousePos.y < y + ym) {
            character_select = 1;
        }
        else {
            character_select = 2;
        }

        //draw the sprite
        if (character_select == 1) ctx.filter = 'none';
        else ctx.filter = 'grayscale(100%)';
        if (man_select.loaded) ctx.drawImage(man_select, xm, 0 + ym, x, y);

        //draw the sprite
        if (character_select == 2) ctx.filter = 'none';
        else ctx.filter = 'grayscale(100%)';
        if (woman_select.loaded) ctx.drawImage(woman_select, x + xm, 0 + ym, x, y);
    }
}

//check if the sprite is loaded
intro.onload = function () {
    intro.loaded = true;
    drawScreen();
}

man_select.onload = function () {
    man_select.loaded = true;
    drawScreen();
}

woman_select.onload = function () {
    woman_select.loaded = true;
    drawScreen();
}

man_sprite.onload = function () {
    man_sprite.loaded = true;
    drawScreen();
}

woman_sprite.onload = function () {
    woman_sprite.loaded = true;
    drawScreen();
}

tiles[0].onload = function () {
    tiles[0].loaded = true;
    drawScreen();
}

tiles[1].onload = function () {
    tiles[1].loaded = true;
    drawScreen();
}

tiles[2].onload = function () {
    tiles[2].loaded = true;
    drawScreen();
}

//get the context
var canvas = document.getElementById("canvas");
var ctx = canvas.getContext("2d");

//resize the canvas
function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    drawScreen();
}
resizeCanvas();

//resize the canvas to fill browser window dynamically
window.addEventListener('resize', resizeCanvas, false);

//check where the mouse is
function getMousePos(canvas, evt) {
    var rect = canvas.getBoundingClientRect();
    return {
        x: evt.clientX - rect.left,
        y: evt.clientY - rect.top
    }
}

//add the mouse position listener
canvas.addEventListener('mousemove', function (evt) {
    mousePos = getMousePos(canvas, evt);
    drawScreen();
});

//add the mouse click listener
canvas.addEventListener('click', function (evt) {
    if (game_state == 1) {
        game_state = 2;
        drawScreen();
    }
});
