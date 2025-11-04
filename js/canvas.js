// ================================================= //
// =  USER INPUT ALLOWED IN THE layout() FUNCTION  = //
// ================================================= //

// Used in the draw() function. This is the content to be created on the page.
function layout() {
    // ------------------------------------ //
    // =     ADD CANVAS OBJECTS BELOW     = //
    // ------------------------------------ //
    // e.g. 
    // drawNode(ctx, [681,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
    // or 
    // loopDrawNodes(); - this will loop through the config json file.

    loopDrawNodes();
    loopDrawLinks()
}


// ============================================ //
// =        BELOW DRAWS TO THE CANVAS         = //
// =     ADD LINES TO THE layout() FUNCTION   = //
// ============================================ //

function draw() {
    ctx.clearRect(0, 0, canvas_width, canvas_height);
    ctx.drawImage(background, background_x_pos, background_y_pos, background.width*background_x_scale, background.height*background_y_scale);   

    // NOTE: map_json.Config is still used for static config properties
    if (map_json.Config.show_grid) {
        drawGrid(ctx, canvas_height, canvas_width);
    }

    // draw to canvus
    layout();

    // Draw hover image for graphs
    // NOTE: map_json.Config is still used for static config properties
    if (hoverBox) {
        drawHoverTooltip(ctx, mouse, hoverBox.hoverImage, canvas, map_json.Config.image_width, map_json.Config.image_height);
    }

    // check if anything was drawn to the canvas, if not, redraw or error if too long
    checkCanvasPopulated();
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

// COUNTER FOR CANVAS ACTIONS
var canvas_counter = 0;
var redraw_counter = 0;

// INITIALISE THE JSON ARRAYS
var map_json = json = null;

// NEW GLOBAL VARIABLE FOR STORING RUNTIME CONFIG
var stored_config = null;

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
        
        // --- NEW: Copy map_json to stored_config for runtime modifications
        stored_config = JSON.parse(JSON.stringify(map_json));
        
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
        canvas_height = background.height * background_y_scale;
        canvas_width = background.width * background_x_scale;
    } else {
        // fixed sizing, get the sizing from the config
        canvas_height = map_json.Config.canvas_height;
        canvas_width = map_json.Config.canvas_width;
    }
    if (canvas_width < 1) {
        canvas_width = 400;
    }
    if (canvas_height < 1) {
        canvas_height = 100;
    }

    canvas.height = canvas_height;
    canvas.width = canvas_width;
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
function drawNode(ctx, coordinates = [0,0], dimensions = [20,10], style = {}, data = {}, nodeKey = null) { // MODIFIED SIGNATURE
    var fillText = ''; 
    var fillColor = 'white'; 
    var data_value = null; 
    
    // --- 1. BUILD TEXT & PROCESS DATA VALUE ---
    // Build text
    if (data.header) {
        fillText += data.header;
    }
    // if there is a value set
    if (data.value) {
        // store it outside of the array to stop overwriting existing data
        data_value = data.value; 

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
        
        // check for the data type to set thresholds for colouring (moved here to allow fillcolor to be determined before drawing)
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
    if (data.unit && data_value !== null) { // Only append unit if a value was processed
        fillText += data.unit;
    }

    // --- 2. DETERMINE FONT AND BOX DIMENSIONS (NEW LOGIC) ---
    let actualDimensions = [...dimensions]; // Local copy of dimensions
    let actualFontSize;
    let fontFamily = style.font || "monospace";
    const textPadding = 10; // Padding around the text

    // Determine font size
    let configuredFontSize = style.font_size;
    if (configuredFontSize === 0 || configuredFontSize === "auto") {
        // If dimensions are fixed, use original fitTextToBox logic. 
        // If dimensions are auto, default to 12px for initial measurement.
        if (dimensions[0] === 'auto' || dimensions[1] === 'auto') {
            actualFontSize = 12; // Default size for auto-sizing
        } else {
            // Use current dimensions to fit the text (original logic)
            var box = { x: coordinates[0], y: coordinates[1], width: dimensions[0], height: dimensions[1] };
            actualFontSize = fitTextToBox(ctx, fillText, box, fontFamily);
        }
    } else {
        actualFontSize = configuredFontSize;
    }
    
    // Set font for measurement
    ctx.font = `${actualFontSize}px ${fontFamily}`;
    
    // Measure text
    const textMetrics = ctx.measureText(fillText);
    const textWidth = textMetrics.width;
    // Note: Since textBaseline is "middle" and height is less trivial, 
    // we use a simple 'font size' as an approximation of text height.
    const textHeight = actualFontSize; 

    // Adjust width if 'auto'
    if (dimensions[0] === 'auto') {
        actualDimensions[0] = textWidth + textPadding; 
    }
    // Adjust height if 'auto'
    if (dimensions[1] === 'auto') {
        actualDimensions[1] = textHeight + textPadding;
    }

    // If 'auto' font size was used but the auto dimensions are now too small, 
    // we need to re-run the fitTextToBox logic (only if both are auto or fixed)
    if ((configuredFontSize === 0 || configuredFontSize === "auto") && (dimensions[0] !== 'auto' && dimensions[1] !== 'auto')) {
        var finalBox = { x: coordinates[0], y: coordinates[1], width: actualDimensions[0], height: actualDimensions[1] };
        actualFontSize = fitTextToBox(ctx, fillText, finalBox, fontFamily);
    }
    
    // Use the final calculated dimensions and font size
    const finalWidth = actualDimensions[0];
    const finalHeight = actualDimensions[1];


    // --- NEW LOGIC: Store the final dimensions to stored_config ---
    if (nodeKey && stored_config?.Nodes[nodeKey]) {
        // Store the actual drawn dimensions for use by links (if originally 'auto')
        stored_config.Nodes[nodeKey].dimension_x = finalWidth;
        stored_config.Nodes[nodeKey].dimension_y = finalHeight;
        
        // Also store the final calculated font size for consistency if needed later
        if (configuredFontSize === 0 || configuredFontSize === "auto") {
             stored_config.Nodes[nodeKey].style.font_size = actualFontSize;
        }
        // Store final color
        stored_config.Nodes[nodeKey].data.fillColor = fillColor;
    }


    // --- 3. DRAW BOX & INTERACTIVE INFO ---
    if (data.url && data.image) {
        interactiveBoxes.push({'x': coordinates[0], 'y': coordinates[1], 'width': finalWidth, 'height': finalHeight, 'link': resolveArrayPath(json, data.url), 'hoverImage': resolveArrayPath(json, data.image)});
    }

    // Fill background
    ctx.fillStyle = fillColor;
    ctx.fillRect(coordinates[0], coordinates[1], finalWidth, finalHeight);
    canvas_counter ++;

    // Draw border
    ctx.beginPath();
    ctx.lineWidth = style.lineWidth || 1;
    ctx.strokeStyle = style.lineColor || 'black';
    ctx.rect(coordinates[0], coordinates[1], finalWidth, finalHeight);
    ctx.stroke();
    canvas_counter ++;

    // --- 4. DRAW TEXT ---
    let fontColor = style.font_color || "auto";
    
    ctx.font = `${actualFontSize}px ${fontFamily}`;
    ctx.textAlign = "center"; 
    ctx.textBaseline = "middle";
    // check if font_color = "auto" and adjust based on the background color of the box
    if (fontColor == "auto") {
        fontColor = bestTextColor(fillColor); // pick best color based on background color - this can change so is important
    }
    ctx.fillStyle = fontColor || "black"; // Text color
    ctx.fillText(fillText, coordinates[0] + (finalWidth / 2), coordinates[1] + (finalHeight / 2));
    canvas_counter ++;
}

// LOOP THROUGH THE JSON AND DRAW THE SENSORS
function loopDrawNodes() {
    const nodes = stored_config.Nodes; // <-- CHANGED: Use stored_config
    // Loop over all nodes in the Nodes object
    Object.keys(nodes).forEach((nodeKey) => { // <-- CHANGED: Iterate over keys
        const node = nodes[nodeKey]; // Get the node object
        // check if node is enabled for drawing
        if (node.draw === true || node.draw === undefined) {
            node.data.value = evaluateExpression(json, node.data.value) ?? null;
            
            drawNode(
                ctx, // canvas
                [node.position_x, node.position_y], // coordinates
                [node.dimension_x, node.dimension_y], // dimensions
                node.style, // style
                node.data, // data
                nodeKey // <-- NEW: Pass the node's key
            );
        }
    });
}

// LOOP THROUGH THE JSON AND DRAW THE LINKS
function loopDrawLinks() {
    const links = stored_config.Links; // <-- CHANGED: Use stored_config
    // Loop over all links in the Links object
    Object.values(links).forEach((link) => {
        // check if the link is enabled for drawing
        if (link.draw === true || link.draw === undefined) {
            link.data.value = evaluateExpression(json, link.data.value) ?? null;
            var node_a = link.nodes[0];
            var node_b = link.nodes[1];
    
            var node_a_config = stored_config.Nodes[node_a.node]; // <-- CHANGED: Use stored_config
            var node_b_config = stored_config.Nodes[node_b.node]; // <-- CHANGED: Use stored_config
            
            // check if the A end is drawn
            if (node_a_config.draw === true || node_a_config.draw === undefined) {
                // check if the B end is drawn
                if (node_b_config.draw === true || node_b_config.draw === undefined) {
                    drawLinkArrow(
                        ctx, // canvas
                        getAnchorPoint(
                            [node_a_config.position_x, node_a_config.position_y], 
                            [node_a_config.dimension_x, node_a_config.dimension_y], // Now guaranteed to be numeric
                            node_a.anchor, 
                            node_a.offset
                        ), // start coordinates
                        getAnchorPoint(
                            [node_b_config.position_x, node_b_config.position_y], 
                            [node_b_config.dimension_x, node_b_config.dimension_y], // Now guaranteed to be numeric
                            node_b.anchor, 
                            node_b.offset
                        ), // end coordinates
                        link.style
                    );
                } else {
                    // debugging
                    // console.log(node_b_config.name +' has not been enabled for drawing. Link not drawn.');
                }
            } else {
                // debugging
                // console.log(node_a_config.name +' has not been enabled for drawing. Link not drawn.');
            }
        }
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
        case 'power_amps_feed':
            u_thold = 1152;
            l_thold = 0.5;
            ok_color = '#1ac44a';
            u_color = 'red';
            l_color = 'red';
            break;
        case 'power_amps_total':
            u_thold = 2305;
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
        case 'temperature_room':
            u_thold = 26;
            l_thold = 20;
            ok_color = "aqua";
            break;
        case 'temperature_sys':
            u_thold = 70;
            l_thold = 0;
            ok_color = "aqua";
            break;
        case 'humidity_room':
            u_thold = 50;
            l_thold = 20;
            ok_color = "pink";
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
// e.g. var value = resolveArrayPath(json, "Nodes[0].data.value");
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

// GET THE ANCHOR POINT OF A BOX BASED ON COMPASS DIRETIONS
function getAnchorPoint(coords = [0, 0], dimensions = [0, 0], anchor = "C", offset = [0, 0]) {
    let [x, y] = coords;
    let [w, h] = dimensions;
    let px = x, py = y;

    switch(anchor) {
        case 'N': px = x + w/2; py = y; break;          // North
        case 'S': px = x + w/2; py = y + h; break;      // South
        case 'E': px = x + w; py = y + h/2; break;      // East
        case 'W': px = x; py = y + h/2; break;          // West
        case 'NE': px = x + w; py = y; break;           // North-East
        case 'NW': px = x; py = y; break;               // North-West
        case 'SE': px = x + w; py = y + h; break;       // South-East
        case 'SW': px = x; py = y + h; break;           // South-West
        case 'C': px = x + w/2; py = y + h/2; break;    // Center
        default: px = x + w/2; py = y + h/2;            // Center
    }

    // Apply offset if given as array [xOffset, yOffset]
    if (Array.isArray(offset)) {
        px += offset[0] || 0;
        py += offset[1] || 0;
    } else if (typeof offset === 'object') {  // fallback for {x, y} object
        px += offset.x || 0;
        py += offset.y || 0;
    } else if (typeof offset === 'number') {  // optional: shorthand number applies to y
        py += offset;
    }

    return { x: px, y: py };
}

// DRAW A LINK ARROW BASED ON FROM AND TO COORDINATES AND OTHER PARAMS
function drawLinkArrow(ctx, start, end, style) {
    const headLength = style.width * 2.5; // arrowhead size proportional to line width
    const angle = Math.atan2(end.y - start.y, end.x - start.x);

    // Draw a filled arrow (shaft + head) given shaft width and arrowhead size
    const drawArrowShape = (shaftWidth, arrowHeadLength, fillColor, outline = false) => {
        // Line end slightly overlapping arrowhead by 1px
        const lineEnd = {
            x: end.x - (arrowHeadLength - arrowHeadLength/7 ) * Math.cos(angle),
            y: end.y - (arrowHeadLength - arrowHeadLength/7 ) * Math.sin(angle)
        };

        // Shaft as a filled rectangle
        const perpX = Math.sin(angle) * shaftWidth / 2;
        const perpY = -Math.cos(angle) * shaftWidth / 2;

        ctx.fillStyle = fillColor;
        ctx.beginPath();
        ctx.moveTo(start.x - perpX, start.y - perpY);
        ctx.lineTo(lineEnd.x - perpX, lineEnd.y - perpY);
        ctx.lineTo(lineEnd.x + perpX, lineEnd.y + perpY);
        ctx.lineTo(start.x + perpX, start.y + perpY);
        ctx.closePath();
        if (!outline) {
            ctx.fill();
            canvas_counter ++;
        } else {
            ctx.lineWidth = style.line_width || 1;
            ctx.strokeStyle = style.line_wolor || 'black';
            ctx.stroke();
            canvas_counter ++;
        }

        // Arrowhead as filled triangle
        ctx.beginPath();
        ctx.moveTo(end.x, end.y);
        ctx.lineTo(
            end.x - arrowHeadLength * Math.cos(angle - Math.PI / 6),
            end.y - arrowHeadLength * Math.sin(angle - Math.PI / 6)
        );
        ctx.lineTo(
            end.x - arrowHeadLength * Math.cos(angle + Math.PI / 6),
            end.y - arrowHeadLength * Math.sin(angle + Math.PI / 6)
        );
        ctx.closePath();
        if (!outline) {
            ctx.fill();
            canvas_counter ++;
        } else {
            ctx.lineWidth = style.line_width || 1;
            ctx.strokeStyle = style.line_wolor || 'black';
            ctx.stroke();
            canvas_counter ++;
        }
        
    };

    

    // Draw main arrow on top
    drawArrowShape(style.width, headLength, style.color);
    // Draw outline first if specified
    if (style.line_color && style.line_width > 0) {
        drawArrowShape(style.width, headLength, style.color, true);
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

// EVALUATE ANY MATH EXPRESSION IN A STRING
function evaluateExpression(json, expression) {
    if (typeof expression === "number") return expression;
    if (typeof expression !== "string") return null;

    // If it's just a number string
    if (!isNaN(parseFloat(expression))) return parseFloat(expression);

    // Replace any {NODE} references
    let workingExpression = expression;
    if (map_json && map_json.Nodes) {
        workingExpression = resolveNodeReferences(json, expression);
    }

    // Replace object paths (e.g. localhost.ports[0].value)
    const pathRegex = /[A-Za-z_$][\w$]*(?:\[\d+\])?(?:\.[A-Za-z_$][\w$]*(?:\[\d+\])?)*/g;

    const replaced = workingExpression.replace(pathRegex, (match) => {
        const value = resolveArrayPath(json, match);
        if (value === undefined || value === null) return 0;
        if (typeof value === "number") return value;
        const parsed = parseFloat(value);
        return isNaN(parsed) ? 0 : parsed;
    });

    try {
        // eslint-disable-next-line no-new-func
        return Function(`"use strict"; return (${replaced});`)();
    } catch (err) {

        return null;
    }
}

// RESOLVE NODE NAMES IN THE VALUE, CALCULATING THE MATH TOO
function resolveNodeReferences(json, expression) {
    if (typeof expression !== "string" || !map_json?.Nodes) return expression;

    return expression.replace(/\{([\w-]+)\}/g, (match, nodeName) => {
        const node = map_json.Nodes?.[nodeName];
        if (!node) {
            return 0;
        }

        const valuePath = node.data?.value;
        const mathExpr = node.data?.value_math ?? "";
        const baseValue = resolveArrayPath(json, valuePath);

        // Combine baseValue + value_math, e.g. "42*8/1000"
        const combined = `${baseValue}${mathExpr}`;

        // Recursively evaluate that subexpression
        const evaluated = evaluateExpression(json, combined);

        return (typeof evaluated === "number" && !isNaN(evaluated)) ? evaluated : 0;
    });
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

// CHECK IF ANYTHING WAS DRAWN TO THE CANVAS
async function checkCanvasPopulated() {
    if (canvas_counter == 0) {
        if (redraw_counter < 2) {
            redraw_counter++;
            await sleep(1000);
            draw();
        } else {
            alert('No objects have been drawn on the Canvas. Please check the JSON config file.');
        }
    } else {
        if (canvas.width == 0 && canvas.height == 0) {
            window.location.reload();
        }
    }
}

// HANDLES THE DRAWING OF OVERLIB IMAGES ON HOVER
// THE IMAGE WILL ALWAYS BE WITHING THE CANVAS BOUNDARIES
function drawHoverTooltip(ctx, mouse, imageSrc, canvas, width = 400, height = 200, margin = 10) {
    const tooltip = new Image();
    tooltip.src = imageSrc;
    tooltip.onload = function () {
        let drawX = mouse.x + 10; // default: right
        let drawY = mouse.y + 10; // default: below

        // Horizontal adjustment
        if (drawX + width + margin > canvas.width) {
            drawX = mouse.x - width - 10; // flip left
        }
        if (drawX < margin) {
            drawX = margin; // clamp to left edge
        }

        // Vertical adjustment
        if (drawY + height + margin > canvas.height) {
            drawY = mouse.y - height - 10; // flip above
        }
        if (drawY < margin) {
            drawY = margin; // clamp to top
        }

        ctx.drawImage(tooltip, drawX, drawY, width, height);
    };
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

    // show coordinates (relative to canvas)
    const coords_x = Math.floor(event.clientX - rect.left);
    const coords_y = Math.floor(event.clientY - rect.top);
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