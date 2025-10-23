// ================================================= //
// =  USER INPUT ALLOWED IN THE layout() FUNCTION  = //
// ================================================= //

// Used in the draw() function. This is the content to be created on the page.
function layout() {
    // ------------------------------------ //
    // =     ADD CANVAS OBJECTS BELOW     = //
    // ------------------------------------ //
    // e.g. 
    // drawSensor(ctx, [681,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
    // or 
    // loopDrawSensors(); - this will loop through the config json file.

    loopDrawSensors();
}


// ============================================ //
// =        BELOW DRAWS TO THE CANVAS         = //
// =     ADD LINES TO THE layout() FUNCTION   = //
// ============================================ //

function draw() {
    ctx.clearRect(0, 0, canvas_width, canvas_height);
    ctx.drawImage(background, background_x_pos, background_y_pos, background.width*background_x_scale, background.height*background_y_scale);   

    // Uncomment below to show a grid on the canvas
    // drawGrid(ctx, canvas_height, canvas_width);

    // draw to canvus
    layout();

    // Draw hover image for graphs
    if (hoverBox) {
        const tooltip = new Image();
        tooltip.src = hoverBox.hoverImage;
        tooltip.onload = function () {
            ctx.drawImage(tooltip, mouse.x + 10, mouse.y + 10, 400, 200); // size: 400x200
        };
    }
}



// ======================================================================





// ============================================ //
// =       DO NOT TOUCH ANY INFO BELOW        = //
// ============================================ //

// ERROR IF A CONFIG WAS NOT LOADED
if (typeof map_file === 'undefined') {
    document.getElementById('canvas-wrapper').innerText = "No config loaded.";
    throw new FatalError("No config loaded.");
}

// SETUP THE CANVAS OBJECT
var canvas = document.getElementById('canvas');
var ctx = canvas.getContext("2d");
var background = new Image();

// INITIALISE THE JSON ARRAYS
var map_json = json = null;

// SET DEFAULT VALUES TO BE OVERWRITTEN LATER
let interactiveBoxes = [];
let mouse = { x: 0, y: 0 };
let hoverBox = null;
let grid = { width:  0, height: 0 }
var refresh_counter = 0;
var background_img = 'img/default.png'; // overwritten by map config
var background_x_pos = background_y_pos = 0;
var background_x_scale = 1;
var background_y_scale = 1;

// CONVERT THE CONFIG AND START THE BUILD
(async function initMap() {
    try {
        // Wait for the JSON config
        await loadMapJSON();
        updatePageConfigSettings();
        setupBackgroundScaling();
        setupCanvas();
        setupBackgroundAnchor();

        // Wait for image to load
        background.onload = function () {
            // Build the canvas now that both JSON & background are ready
            build(); 
        };
    } catch (err) {
        console.error("Error loading map or background:", err);
    }
})();



// ================== Sub-Functions ==================


// LOAD IN THE MAP JSON AND SET PARAMS
async function loadMapJSON() {
    map_json = await getData(map_file);

    // Check if the config file is valid JSON, and if not error
    if (!map_json || typeof map_json !== 'object') {
        document.getElementById('canvas-wrapper').innerText = "Config is not valid JSON data or failed to load.";
        console.error("Fatal: Config is not valid JSON data or failed to load.");
        throw new Error("Fatal: Config is not valid JSON data or failed to load.");
    }

    data_url = map_json.Config.data_url;

    // Determine background image (use default if not found)
    background_img = map_json.Config.background_img || 'img/default.png';
    background.src = background_img;
}

// SET UP THE BACKGROUND IMAGE WITH SCALING
function setupBackgroundScaling() {
    if (!map_json.Config.background_scale) return;

    let background_scale = map_json.Config.background_scale;

    // If scale is array [x, y]
    if (Array.isArray(background_scale)) {
        background_x_scale = background_scale[0];
        // Check if there are 2 parameters (0,1)
        background_y_scale = background_scale.length > 1 ? background_scale[1] : background_scale[0];
    } else {
        background_x_scale = background_y_scale = background_scale;
    }

    // Check to make sure they are positive numbers (can be float)
    if (!checkValidNumber(background_x_scale)) {
        console.log('Invalid background_x_scale, defaulting to 1');
        background_x_scale = 1;
    }
    if (!checkValidNumber(background_y_scale)) {
        console.log('Invalid background_y_scale, defaulting to 1');
        background_y_scale = 1;
    }
}

// SETUP THE CANVAS 
function setupCanvas() {
    if (!map_json.Config.fixed_canvas_size) {
        // if not a fixed sizing, scale to background image (including the scaling)
        canvas.height = canvas_height = background.height * background_y_scale;
        canvas.width = canvas_width = background.width * background_x_scale;
    } else {
        // fixed sizing, get the sizing from the config
        canvas.height = canvas_height = map_json.Config.canvas_height;
        canvas.width = canvas_width = map_json.Config.canvas_width;
    }
}

// SET THE ANCHOR POINT OF THE BACKGROUND IMAGE BASED ON background_img_anchor IN CONFIG
function setupBackgroundAnchor() {
    // check for background posititon in config, adjust anchor point (valid options: center/middle, left, right, top, bottom)
    const background_anchor = map_json.Config.background_anchor || 'default';

    switch (background_anchor) {
        case 'middle':
        case 'center':
            background_x_pos = (canvas.width - (background.width * background_x_scale)) / 2;
            background_y_pos = (canvas.height - (background.height * background_y_scale)) / 2;
            break;
        case 'left':
            background_x_pos = 0;
            background_y_pos = (canvas.height - (background.height * background_y_scale)) / 2;
            break;
        case 'right':
            background_x_pos = canvas.width - (background.width * background_x_scale);
            background_y_pos = (canvas.height - (background.height * background_y_scale)) / 2;
            break;
        case 'top':
            background_x_pos = (canvas.width - (background.width * background_x_scale)) / 2;
            background_y_pos = 0;
            break;
        case 'bottom':
            background_x_pos = (canvas.width - (background.width * background_x_scale)) / 2;
            background_y_pos = canvas.height - (background.height * background_y_scale);
            break;
        case 'top-left':
        case 'default':
        default:
            background_x_pos = 0;
            background_y_pos = 0;
    }
}

// BUILD THE CANVAS - SEPERATED SO THAT IT CAN BE RE-RUN TO REFRESH DATA
function build() {
    (async() => {
        // get the data json
        json = await getData(data_url);

        draw(); // draw the canvas
        populateDimensions(); // update the canvas dimensions
        updateTimestamp(); // write the timestamp
        periodicUpdate(); // reload data periodically if enabled
    })();
}

// DRAW SENSOR IN PRESET METHOD
// e.g. 
// drawSensor(
//          ctx, 
//          [681,420], 
//          [50,30], 
//          { lineWidth: 2, strokeStyle: 'black' }, 
//          {'header': null, 'value': '0.1', 'value_math': '*0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'}
//  );
function drawSensor(ctx, coordinates = [0,0], dimensions = [20,10], style = {}, data = {}) {
    var fillText = ''; // default fillText to empty - this is for the prefix of the data
    var fillColor = 'white'; // default background color

    // Build text
    if (data.header) {
        fillText += data.header;
    }
    // if there is a value set
    if (data.value) {
        // store it outside of the array to stop overwriting existing data
        var data_value = data.value; 

        // check if there is a math calculation to do
        if (data.value_math) {
            // run the math
            var mathed_data = applyMath(data_value, data.value_math);
            // check it changed if not dont update
            if (mathed_data !== data_value) { data_value = mathed_data; }
            // check for any decimal places adjustments
            if (data.value_float_num) {
                // adjust the float decimals 
                var floated_data = data_value.toFixed(data.value_float_num);
                // if there is change, update the value
                if (floated_data !== data_value) { data_value = floated_data; }
            }
        }

        // Add the header to the data as a prefix
        fillText += data_value;

        // check for the data type to set thresholds for colouring
        if (data.type) {
            var thold = thresholds(data_value, data.type);

            if (data_value > thold.upper) { // set color if over upper threshold
                fillColor = thold.upper_color;
            } else if (data_value < thold.lower) { // set color if under lower threshold
                fillColor = thold.lower_color;
            } else { // set color to the ok_color - if none define, make it grey
                fillColor = thold.ok_color || 'grey';
            }
        }
    }
    if (data.unit && data_value) {
        fillText += data.unit;
    }
    if (data.url && data.image) {
        interactiveBoxes.push({'x': coordinates[0], 'y': coordinates[1], 'width': dimensions[0], 'height': dimensions[1], 'link': data.url, 'hoverImage': data.image});
    }

    // Fill background
    ctx.fillStyle = fillColor;
    ctx.fillRect(coordinates[0], coordinates[1], dimensions[0], dimensions[1]);

    // Draw border
    ctx.beginPath();
    ctx.lineWidth = style.lineWidth || 1;
    ctx.strokeStyle = style.lineColor || 'black';
    ctx.rect(coordinates[0], coordinates[1], dimensions[0], dimensions[1]);
    ctx.stroke();

    // Draw text
    let fontSize = style.font_size || "auto"; // get the font size or default to auto
    let fontFamily = style.font || "monospace";
    let fontColor = style.font_color || "auto";
    // check if the font size is 0 - automatically make it fit the box
    if (fontSize === 0 || fontSize === "auto") {
        var box = { x: coordinates[0], y: coordinates[1], width: dimensions[0], height: dimensions[1] };
        fontSize = fitTextToBox(ctx, fillText, box, fontFamily);
    }
    ctx.font = `${fontSize}px ${fontFamily}`;
    ctx.textAlign = "center"; 
    ctx.textBaseline = "middle";
    // check if font_color = "auto" and adjust based on the background color of the box
    if (fontColor == "auto") {
        fontColor = bestTextColor(fillColor); // pick best color based on background color - this can change so is important
    }
    ctx.fillStyle = fontColor || "black"; // Text color
    ctx.fillText(fillText, coordinates[0] + (dimensions[0] / 2), coordinates[1] + (dimensions[1] / 2));
}

// LOOP THROUGH THE JSON AND DRAW THE SENSORS
function loopDrawSensors() {
    var sensors = map_json.Sensors;
    sensors.forEach((sensor) => {
        sensor.data.value = resolveArrayPath(json, sensor.data.value) ?? null;
        drawSensor(
            ctx, // canvas
            [sensor.position_x,sensor.position_y], // coordinates
            [sensor.dimension_x,sensor.dimension_y], // dimensions
            sensor.style, // style
            sensor.data // data
        );
    });
}

// DRAW A GRID OVERLY ON THE CANVAS. HELPFUL FOR SHOWING COORDINATES
function drawGrid(ctx, canvasHeight = null, canvasWidth = null) {
    grid.height = canvasHeight/10;
    grid.width = canvasWidth/10;

    ctx.beginPath();
    ctx.lineWidth = 1;
    ctx.strokeStyle = "lightgrey";

    for(let x = 0; x <= canvasHeight*grid.height; x += canvasHeight / grid.height){
        ctx.moveTo(x, 0)
        ctx.lineTo(x, canvasHeight)
    }
    for(let y = 0; y <= canvasWidth*grid.width; y += canvasWidth / grid.width){
        ctx.moveTo(0, y)
        ctx.lineTo(canvasWidth, y)
    }
    ctx.stroke();
}

// THRESHOLDS - USED TO COLOUR THE SENSORS BASED ON DATA 
function thresholds(data_in, type) {
    var u_thold  = 0;
    var l_thold  = 0;
    var ok_color = '#1ac44a';
    var u_color  = 'red';
    var l_color  = 'red'
    switch (type) {
        case 'power_amps':
            u_thold = 32;
            l_thold = 0.5;
            ok_color = '#1ac44a';
            u_color = 'red';
            l_color = 'red';
            break;
        case 'power_kw':
            u_thold = 32;
            l_thold = 0.5;
            break;
        case 'data':
            u_thold = 1000000;
            l_thold = 1;
            break;
        default:
            u_told = 0;
            l_thold = 0;
    }

    return {
        'upper' : u_thold, 
        'upper_color': u_color,
        'lower': l_thold,
        'lower_color': l_color,
        'ok_color': ok_color
    };
}

// FETCH A FILE
async function getData($file) {
    try {
        const response = await fetch($file);
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Fetch error:', error);
    }
}

// TRANSLATE STRING TO ARRAY PATH
// e.g. var value = resolveArrayPath(json, "Sensors[0].data.value");
function resolveArrayPath(obj, path) {
    if (typeof path !== "string") {
        // If it's not a string (e.g. already a value), just return it as-is
        return path;
    }

    return path
        .split('.')
        .reduce((acc, key) => {
            const match = key.match(/^(\w+)\[(\d+)\]$/);
            if (match) {
                return acc?.[match[1]]?.[parseInt(match[2])];
            }
            return acc?.[key];
        }, obj);
}

// PERIDOICALLY RE-RUN THE build() FUNCTION TO RE-LOAD THE CANVAS AND DATA
// WILL ALSO REFRESH THE PAGE AFTER 30 MINS
async function periodicUpdate() {
    if (map_json.Config.enable_periodic_update) {  
        console.log('Updated at: '+getDateTime());
        // refresh counter 
        await sleep(60000); // 1 minute sleep time before getting new data

        // add to counter
        refresh_counter += 1
        // refresh at 30 minutes
        if (refresh_counter === 30) {
            window.location.reload();
        }
        build();
    } else {
        console.log('Refreshing disabled.');
    }
}

// SLEEP TIMER FOR USE IN periodicUpdate()
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// POPULATE THE CANVUS DIMENSIONS
function populateDimensions() {
    var div = document.getElementById('dimensions');
    var dimension_text = 'Dimensions: '+canvas_width+'px x '+canvas_height+'px';
    div.innerText = dimension_text;
}

// UPDATE TIMESTAMP - IN A FUNCTION TO MAKE IT MORE READABLE
function updateTimestamp() {
    document.getElementById('timestamp').innerText = 'Canvas generated: '+getDateTime();
}

// GET CURRENT DATE + TIME TIMESTAMP FOR CANVAS
function getDateTime() {
    const now = new Date();

    const pad = (n) => n.toString().padStart(2, '0');

    const hours = pad(now.getHours());
    const minutes = pad(now.getMinutes());
    const seconds = pad(now.getSeconds());

    const day = pad(now.getDate());
    const month = pad(now.getMonth() + 1); // Months are 0-based
    const year = pad(now.getFullYear() % 100); // Last 2 digits of year

    return `${hours}:${minutes}:${seconds} ${day}/${month}/${year}`;
}

// CHECK IF THE TEXT FITS THE BOX AND SCALE THE FONT SIZE TO FIT
function fitTextToBox(ctx, text, box, fontFamily) {
  let fontSize = box.height; // start large
  ctx.font = `${fontSize}px ${fontFamily}`;

  // Measure and shrink until it fits (with 1px padding all round")
  while (ctx.measureText(text).width > box.width-2 || fontSize > box.height-2) {
    fontSize--;
    ctx.font = `${fontSize}px ${fontFamily}`;
  }
  
  return fontSize;
}

// CHECK THE INPUT DATA IS A VALID POSITIVE NUMBER - CAN BE A FLOAT/DECIMAL
function checkValidNumber(n) {
    if (typeof n !== "number" || isNaN(n)) {
        return false;
    }
    return true;
}

// APPLY MATH DATA FROM JSON CONFIG FILE TO THE VALUE
function applyMath(value, mathStr) {
    if (!mathStr) return value; // no math to apply

    try {
        const fn = new Function('x', `return x ${mathStr};`);
        return fn(value);
    } catch (err) {
        console.error('Failed to apply math:', err);
        return value;
    }
}

// CONVERTS CSS COLOR STRING TO RGB OBJECT {r, g, b}
function parseColor(color) {
    color = color.trim().toLowerCase();

    // Hex format (#fff or #ffffff)
    if (color[0] === "#") {
        let hex = color.slice(1);
        if (hex.length === 3) {
            hex = hex.split("").map(h => h + h).join(""); // convert #abc => #aabbcc
        }
        const intVal = parseInt(hex, 16);
        return {
            r: (intVal >> 16) & 255,
            g: (intVal >> 8) & 255,
            b: intVal & 255
        };
    }

    // RGB format: rgb(r,g,b)
    const rgbMatch = color.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    if (rgbMatch) {
        return {
            r: parseInt(rgbMatch[1]),
            g: parseInt(rgbMatch[2]),
            b: parseInt(rgbMatch[3])
        };
    }

    // Named CSS color
    const ctx = document.createElement("canvas").getContext("2d");
    ctx.fillStyle = color;
    ctx.fillRect(0, 0, 1, 1);
    const data = ctx.getImageData(0, 0, 1, 1).data;
    return { r: data[0], g: data[1], b: data[2] };
}

// PICK BEST TEXT COLOR - BLACK OR WHITE - TO BE USED ON DEFINED COLOUR
function bestTextColor(bgColor) {
    const { r, g, b } = parseColor(bgColor);

    // Calculate perceived luminance
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

    return luminance > 0.5 ? "black" : "white";
}

// TOGGLE VISIBILITY OF CONFIG ITEMS
function updatePageConfigSettings() {
    const config = map_json.Config;
    document.getElementById('coords').hidden     = !config.show_coordinates;
    document.getElementById('timestamp').hidden  = !config.show_timestamp;
    document.getElementById('dimensions').hidden = !config.show_dimensions;
    document.getElementById('config').hidden     = !config.show_config;

    if (config.page_title) document.title = config.page_title || "Untitled Map";
    if (config.page_header && config.show_page_header) {
        document.getElementById('page-header').innerText = config.page_header;
        document.getElementById('page-header').hidden = false;
    }

    if(config.show_config) {
        var pre = document.createElement('pre');
        pre.innerText = JSON.stringify(map_json, undefined, 2);
        pre.setAttribute("style", "max-width:min-content");

        document.getElementById('config').innerHTML = "Config file: '"+map_file+"'";
        document.getElementById('config').appendChild(pre);
    }
}

// HANDLES THE MOVEMENT OF THE MOUSE OVER THE CANVAS
// USED TO SHOW COORDINATES AND THE HOVER IMAGES
function handleMouseMove(event) {
    const rect = canvas.getBoundingClientRect();
    mouse.x = event.clientX - rect.left;
    mouse.y = event.clientY - rect.top;

    hoverBox = null;

    for (let box of interactiveBoxes) {
        if (
            mouse.x >= box.x && mouse.x <= box.x + box.width &&
            mouse.y >= box.y && mouse.y <= box.y + box.height
        ) {
            hoverBox = box;
            break;
        }
    }
    canvas.style.cursor = hoverBox ? 'pointer' : 'default'; 
    draw(); // Re-render canvas

    // show coordinates
    coords_x = Math.floor(event.clientX - rect.left);
    coords_y = Math.floor(event.clientY - rect.left);
    document.getElementById('coords').innerText = `x: ${coords_x}, y: ${coords_y}`;
}

// HANDLES CLICKING THE CANVAS OBJECTS
// THIS JUST REGISTERED THE COORDINATES OF THE CLICK AND LOADS THE URL REGISTERED TO THE AREA
function handleClick(event) {
    if (hoverBox && hoverBox.link) {
        window.open(hoverBox.link, "_blank");
    }
}

// EVENT LISTENERS FOR MOUSE ACTIONS
canvas.addEventListener('mousemove', handleMouseMove);
canvas.addEventListener('click', handleClick);