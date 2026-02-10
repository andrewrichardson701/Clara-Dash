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
    loopDrawLinks();
    loopDrawKeys();
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
        // drawHoverTooltip(ctx, mouse, hoverBox.hoverImage, canvas, map_json.Config.image_width, map_json.Config.image_height);
        drawHoverTooltip(
            ctx,
            mouse,
            hoverBox.hoverImage,
            canvas,
            map_json.Config.image_width,
            map_json.Config.image_height,
            10,
            hoverBox.name || "" // pass name for display
        );
    } 

    // check and clear cache
    if (!hoverBox && hoverCache.currentSrc) {
        hoverCache.currentSrc = null;
        hoverCache.image = null;
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
if (!canvas) {
    var canvas = document.getElementById(canvas_id || 'canvas'); // canvas_id is generated via canvas.php
}
var ctx = canvas.getContext("2d");
var background = new Image();

// COUNTER FOR CANVAS ACTIONS
var canvas_counter = 0;
var redraw_counter = 0;

// INITIALISE THE JSON ARRAYS
var map_json = json = null;

// NEW GLOBAL VARIABLE FOR STORING RUNTIME CONFIG
var stored_config = null;

// SET EMPTY CACHE
let hoverCache = {
    currentSrc: null,
    image: null
};

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
        
        // Copy map_json to stored_config for runtime modifications
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

        // Reset stored_config to a fresh deep copy of the original map_json.
        stored_config = JSON.parse(JSON.stringify(map_json));

        draw(); // draw the canvas
        populateDimensions(); // update the canvas dimensions
        updateTimestamp(); // write the timestamp
        periodicUpdate(); // reload data periodically if enabled
    })();
}

// DRAW SENSOR IN PRESET METHOD
function drawNode(ctx, coordinates = [0,0], dimensions = [20,10], style = {}, data = {}, nodeKey = null) { 
    var fillText = ''; 
    var fillColor = style.color || "auto"; 
    var data_value = null; 
    const anchor = style.anchor || "NW";
    
    // BUILD TEXT & PROCESS DATA VALUE 
    // Build text
    if (data.header) {
        fillText += data.header;
    }
    // if there is a value set
    if (data.value || data.value == 0) {
        data.value = evaluateExpression(json, data.value) ?? 0;

        // store it outside of the array to stop overwriting existing data
        data_value = calculateValue(data.value, data.value_math, data.value_float_num) ?? 0;

        // Add the header to the data as a prefix
        fillText += data_value;

        // Check for threshold_value and replace it for threshold checking
        if (data.threshold_value && data.threshold_value !== '' && data.threshold_value !== null) {
            // evaluate the value to convert any array keys and do math values
            data.threshold_value = evaluateExpression(json, data.threshold_value) ?? null;
            // store it outside of the array to stop overwriting existing data
            data_threshold_value = calculateValue(data.threshold_value, data.value_math, data.value_float_num);
            
            if (data.type && fillColor == "auto") {
                fillColor = thresholds(data_threshold_value, data.type);
            }
        } else {
            if (data.type && fillColor == "auto") {
                fillColor = thresholds(data_value, data.type);
            }
        }
        
        // check for the data type to set thresholds for colouring (moved here to allow fillcolor to be determined before drawing)
    }

    if (data.unit && data_value !== null) { // Only append unit if a value was processed
        fillText += data.unit;
    }

    if (fillColor == "auto") {
        fillColor = "white";
    }

    // DETERMINE FONT AND BOX DIMENSIONS
    let actualDimensions = [...dimensions]; // Local copy of dimensions
    let actualFontSize;
    let fontFamily = style.font || "monospace";
    const textPadding = style.padding || 10; // Padding around the text

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

    coordinates = getBoxStartFromAnchor(
        { x: coordinates[0], y: coordinates[1] },
        [finalWidth, finalHeight],
        anchor,
        style.offset || [0,0]
    );

    // Store the final dimensions to stored_config 
    if (nodeKey && stored_config?.Nodes[nodeKey]) {
        // Store the actual drawn dimensions for use by links (if originally 'auto')
        stored_config.Nodes[nodeKey].dimension_x = finalWidth;
        stored_config.Nodes[nodeKey].dimension_y = finalHeight;
        
        // Also store the final calculated font size for consistency if needed later
        if (configuredFontSize === 0 || configuredFontSize === "auto") {
             stored_config.Nodes[nodeKey].style.font_size = actualFontSize;
        }
        // Store final color
        stored_config.Nodes[nodeKey].style.color = fillColor;
    }

    var hoverName = '';
    // DRAW BOX & INTERACTIVE INFO
    if (data.url && data.image && stored_config?.Nodes[nodeKey]) {
        if (stored_config.Nodes[nodeKey].name) {
            hoverName = stored_config.Nodes[nodeKey].name;
        }
        hoverName += ' ('+nodeKey+')';
        if (resolveArrayPath(json, data.image)) {
            interactiveBoxes.push({
                x: coordinates[0],
                y: coordinates[1],
                width: dimensions[0],
                height: dimensions[1],
                link: resolveArrayPath(json, data.url),
                hoverImage: resolveArrayPath(json, data.image),
                name: hoverName || null
            }); 
        }
    }

    // Fill background
    if (fillColor !== 'null' && fillColor !== 'transparent') {
        ctx.fillStyle = fillColor;
        ctx.fillRect(coordinates[0], coordinates[1], finalWidth, finalHeight);
        canvas_counter ++;
    } else {
        fillColor = "white";
    }

    // Draw border
    ctx.beginPath();
    ctx.lineWidth = style.line_width || 1;
    ctx.strokeStyle = style.line_color || 'black';
    ctx.rect(coordinates[0], coordinates[1], finalWidth, finalHeight);
    ctx.stroke();
    canvas_counter ++;

    // DRAW TEXT 
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

function calculateValue(data_value, value_math, value_float_num) {
    // check if there is a math calculation to do
    if (value_math) {
        // run the math
        var mathed_data = applyMath(data_value, value_math);
        // check it changed if not dont update
        if (mathed_data !== data_value) { data_value = mathed_data; }
    }

    // check for any decimal places adjustments
    if (value_float_num) {
        // adjust the float decimals 
        var floated_data = data_value.toFixed(value_float_num);
        // if there is change, update the value
        if (floated_data !== data_value) { data_value = floated_data; }
    }

    return data_value;
}

// LOOP THROUGH THE JSON AND DRAW THE SENSORS
function loopDrawNodes() {
    const nodes = stored_config.Nodes;
    // Loop over all nodes in the Nodes object
    Object.keys(nodes).forEach((nodeKey) => { 
        const node = nodes[nodeKey]; // Get the node object
        // check if node is enabled for drawing
        if (node.draw === true || node.draw === undefined) {          
            drawNode(
                ctx, // canvas
                [node.position_x, node.position_y], // coordinates
                [node.dimension_x, node.dimension_y], // dimensions
                node.style, // style
                node.data, // data
                nodeKey // Pass the node's key
            );
        }
    });
    
    // preload the images
    interactiveBoxes.forEach(box => {
        if (box.hoverImage && box.hoverImage !== undefined) {
            const img = new Image();
            img.src = box.hoverImage;
            box.preloadedImage = img;
        }
    });
}

// LOOP THROUGH THE JSON AND DRAW THE LINKS
function loopDrawLinks() {
    const links = stored_config.Links;
    // Loop over all links in the Links object
    Object.values(links).forEach((link) => {
        // check if the link is enabled for drawing
        if (link.draw === true || link.draw === undefined) {
            link.data.value = evaluateExpression(json, link.data.value) ?? 0;
            var node_a = link.nodes[0];
            var node_b = link.nodes[1];
    
            var node_a_config = stored_config.Nodes[node_a.node]; 
            var node_b_config = stored_config.Nodes[node_b.node]; 
            
            // check if the A end is drawn
            if (node_a_config.draw === true || node_a_config.draw === undefined) {
                // check if the B end is drawn
                if (node_b_config.draw === true || node_b_config.draw === undefined) {
                    if (!link.node_style) {
                        link.node_style = {
                            "color": "auto",
                            "line_width": 2,
                            "line_color": "black",
                            "font": "monospace",
                            "font_size": 10,
                            "font_color": "black",
                            "padding": 2,
                            "anchor": "C"
                        };
                    }
                    drawLinkArrow(
                       ctx, // canvas
                        getAnchorPoint(
                            [node_a_config.position_x, node_a_config.position_y], 
                            [node_a_config.dimension_x, node_a_config.dimension_y], 
                            node_a.anchor, 
                            node_a.offset
                        ), // start coordinates
                        getAnchorPoint(
                            [node_b_config.position_x, node_b_config.position_y], 
                            [node_b_config.dimension_x, node_b_config.dimension_y], 
                            node_b.anchor, 
                            node_b.offset
                        ), // end coordinates
                        link.style,
                        link.node_style,
                        link.data,
                        link
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
    if (type === undefined || type === null || type == '') {
        type = 'default';
    }
    const tholds = map_json?.Thresholds?.[type];
    if (!Array.isArray(tholds)) return "grey";

    for (const thold of tholds) {
        if (data_in >= thold.lower && data_in < thold.upper) {
            return thold.color;
        }
    }

    return "grey"; // fallback
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
    if (typeof path !== "string" || path.includes('http')) {
        return path;
    }

    const tokens = [];
    const regex =
        /\[(?:'([^']+)'|"([^"]+)")\]|([^.[]+)|\[(\d+)\]/g;

    let match;
    while ((match = regex.exec(path)) !== null) {
        if (match[1]) tokens.push(match[1]);          // single-quoted key
        else if (match[2]) tokens.push(match[2]);     // double-quoted key
        else if (match[3]) tokens.push(match[3]);     // plain key
        else if (match[4]) tokens.push(Number(match[4])); // array index
    }

    return tokens.reduce((acc, key) => acc?.[key], obj);
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

// GET THE TOP-LEFT COORDINATE OF A BOX GIVEN AN ANCHOR POINT
function getBoxStartFromAnchor(anchorPoint = {x:0, y:0}, dimensions = [0,0], anchor = "C", offset = [0,0]) {
    let [w, h] = dimensions;

    let x = anchorPoint.x;
    let y = anchorPoint.y

    switch(anchor) {
        case 'N': x = x - w/2; y = y; break;          // North
        case 'S': x = x - w/2; y = y - h; break;      // South
        case 'E': x = x - w; y = y - h/2; break;      // East
        case 'W': x = x; y = y - h/2; break;          // West
        case 'NE': x = x - w; y = y; break;           // North-East
        case 'NW': x = x; y = y; break;               // North-West
        case 'SE': x = x - w; y = y - h; break;       // South-East
        case 'SW': x = x; y = y - h; break;           // South-West
        case 'C': x = x - w/2; y = y - h/2; break;    // Center
        default: x = x - w/2; y = y - h/2;            // Center
    }

    return [ x, y ];
}

// TRANSFORM THE COORDINATES BASED ON THE ANGLE PROVIDED
function transform(xy,angle,xy0){
    // put x and y relative to x0 and y0 so we can rotate around that
    const rel_x = xy[0] - xy0[0];
    const rel_y = xy[1] - xy0[1];

    // compute rotated relative points
    const new_rel_x = Math.cos(angle) * rel_x - Math.sin(angle) * rel_y;
    const new_rel_y = Math.sin(angle) * rel_x + Math.cos(angle) * rel_y;

    return [xy0[0] + new_rel_x, xy0[1] + new_rel_y];
}

// DRAW A LINK ARROW BASED ON FROM AND TO COORDINATES AND OTHER PARAMS
function drawLinkArrow(ctx, start, end, style, node_style, data, linkKey) {
    let width = style.width || 10;
    let head_length = width * 2.5;
    let head_width = width * 2.5;

    // compute full length and angle
    const dx = end.x - start.x;
    const dy = end.y - start.y;
    const length = Math.sqrt(dx * dx + dy * dy);

    // compute midpoint
    const mid = { x: start.x + dx / 2, y: start.y + dy / 2 };

    // helper to draw a single arrow from p_start to p_end
    function drawSingleArrow(p_start, p_end, data, linkKey) {
        const len = Math.sqrt((p_end.x - p_start.x) ** 2 + (p_end.y - p_start.y) ** 2);
        const ang = Math.atan2(p_end.y - p_start.y, p_end.x - p_start.x) - Math.PI / 2;
        const p0 =  [p_start.x, p_start.y];
        const p50 = [(p_start.x + p_end.x) / 2, (p_start.y + p_end.y) / 2];

        let p1 = [p_start.x + width / 2, p_start.y];
        let p2 = [p_start.x - width / 2, p_start.y];
        let p3 = [p_start.x + width / 2, p_start.y + len - head_length];
        let p4 = [p_start.x - width / 2, p_start.y + len - head_length];
        let p5 = [p_start.x + head_width / 2, p_start.y + len - head_length];
        let p6 = [p_start.x - head_width / 2, p_start.y + len - head_length];
        let p7 = [p_start.x, p_start.y + len];

        // transform points
        p1 = transform(p1, ang, p0);
        p2 = transform(p2, ang, p0);
        p3 = transform(p3, ang, p0);
        p4 = transform(p4, ang, p0);
        p5 = transform(p5, ang, p0);
        p6 = transform(p6, ang, p0);
        p7 = transform(p7, ang, p0);

        // determin the colour of the link
        fillcolor = "white";
        // if there is a value set
        if (data.value) {
            data.value = evaluateExpression(json, data.value) ?? 0;
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

            // check for the data type to set thresholds for colouring (moved here to allow fillcolor to be determined before drawing)
            if (data.type) {
                fillColor = thresholds(data_value, data.type);
            }
        }

        if (style.color == "auto") {
            arrow_color = fillColor;
        } else {
            arrow_color = style.color;
        }

        ctx.beginPath();
        ctx.moveTo(p1[0], p1[1]);
        ctx.lineTo(p3[0], p3[1]);
        ctx.lineTo(p5[0], p5[1]);
        ctx.lineTo(p7[0], p7[1]);
        ctx.lineTo(p6[0], p6[1]);
        ctx.lineTo(p4[0], p4[1]);
        ctx.lineTo(p2[0], p2[1]);
        ctx.lineTo(p1[0], p1[1]);
        ctx.closePath();
        ctx.fillStyle = arrow_color || "white";
        ctx.fill();
        if (style.line_width > 0) {
            ctx.lineWidth = style.line_width || 1;
            ctx.strokeStyle = style.line_color || "black";
            ctx.stroke();
        }
        canvas_counter++;

        if (data && data.draw == true) {
            if (!node_style.anchor){
                node_style.anchor = "C";
            }
            drawNode(
                ctx, 
                p50, 
                ["auto", "auto"], 
                node_style,
                data,
                linkKey
            );
        }
    }

    if (style.line_two_way) {
        if (style.line_direction === "diverge") { // away
            // arrows point away from each other (diverging)
            drawSingleArrow(mid, start, data[0] || null, linkKey);
            drawSingleArrow(mid, end, data[1] || null, linkKey);
        } else if (style.line_direction === "converge") { // towards 
            // default: arrows point towards each other (converging)
            drawSingleArrow(start, mid, data[0] || null, linkKey);
            drawSingleArrow(end, mid, data[1] || null, linkKey);
        } else if (style.line_direction === "reverse") { // swap the arrows
            drawSingleArrow(end, mid, data[0] || null, linkKey);
            drawSingleArrow(start, mid, data[1] || null, linkKey);
        } else {// towards 
            // default: arrows point towards each other (converging)
            drawSingleArrow(start, mid, data[0] || null, linkKey);
            drawSingleArrow(end, mid, data[1] || null, linkKey);
        }
    } else {
        if (style.line_direction === "reverse") {
            drawSingleArrow(end, start, data[0] || null, linkKey);
        } else {
            drawSingleArrow(start, end, data[0] || null, linkKey);
        }
        
    }
}

// DRAW A LINK ARROW BASED ON FROM AND TO COORDINATES AND OTHER PARAMS
function drawLinkArrow_old(ctx, start, end, style) {
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
            ctx.strokeStyle = style.line_color || 'black';
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
    const pathRegex = /(?:\[(?:'[^']+'|"[^"]+")\]|[A-Za-z_$][\w$]*)(?:\[\d+\]|\.(?:[A-Za-z_$][\w$]*|\[(?:'[^']+'|"[^"]+")\]))*/g;

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
function drawHoverTooltip(
    ctx,
    mouse,
    imageSrc,
    canvas,
    width = 400,
    height = 200,
    margin = 10,
    title = ""
) {
    // If new image, load it once and cache it
    if (hoverCache.currentSrc !== imageSrc) {
        hoverCache.image = new Image();
        hoverCache.image.src = imageSrc;
        hoverCache.currentSrc = imageSrc;
    }

    const tooltip = hoverBox.preloadedImage || hoverCache.image;
    if (!tooltip.complete) return; // image not ready yet — skip drawing

    // Measure title space
    const titleFont = "bold 14px monospace";
    const titlePadding = 8;
    const titleHeight = title ? 20 : 0;
    const totalHeight = height + titleHeight + titlePadding * 2;

    // Position
    let drawX = mouse.x + 10;
    let drawY = mouse.y + 10;

    if (drawX + width + margin > canvas.width) drawX = mouse.x - width - 20;
    if (drawX < margin) drawX = margin;
    if (drawY + totalHeight + margin > canvas.height)
        drawY = mouse.y - totalHeight - 20;
    if (drawY < margin) drawY = margin;

    // ---- Background ----
    ctx.save();
    ctx.globalAlpha = 0.9;
    ctx.fillStyle = "white";
    ctx.fillRect(drawX - 5, drawY - 5, width + 10, totalHeight + 10);
    ctx.globalAlpha = 1;
    ctx.strokeStyle = "rgba(0,0,0,0.1)";
    ctx.lineWidth = 1;
    ctx.strokeRect(drawX - 5, drawY - 5, width + 10, totalHeight + 10);
    ctx.restore();

    // ---- Title ----
    if (title) {
        ctx.font = titleFont;
        ctx.fillStyle = "rgba(0,0,0,0.85)";
        ctx.textAlign = "center";
        ctx.textBaseline = "top";
        ctx.fillText(title, drawX + width / 2, drawY + titlePadding);
    }

    // ---- Image ----
    const imageY = drawY + titleHeight + titlePadding * 1.5;
    ctx.drawImage(tooltip, drawX, imageY, width, height);
}

function loopDrawKeys() {
    const keys = map_json.Keys;
    if (!keys) return;

    // Loop through each key group
    for (const keyName in keys) {
        const cfg = keys[keyName];

        if (!cfg.draw) continue; // Skip if not drawn

        drawKey(cfg);
    }
}

function drawKey(cfg) {

    // === TEXT MEASUREMENT ===
    ctx.font = `${cfg.title_font_size}px sans-serif`;
    ctx.textAlign = "left";

    let rowHeight = cfg.font_size + cfg.padding *2 + 12;

    // Determine widest text for box width
    let maxWidth = ctx.measureText(cfg.title).width;
    for (const entry of cfg.entries) {
        if (!entry.draw) { // check if draw is defined and false, if undefined or true, skip
            console.log()
           let w = ctx.measureText(entry.text).width + 30; // 30px color square + gap
            if (w > maxWidth) maxWidth = w; 
        }
    }

    let boxWidth = maxWidth + cfg.box_padding * 2;
    let boxHeight = cfg.title_font_size + cfg.padding + cfg.entries.length * rowHeight + cfg.box_padding * 2;

    // === DRAW BACKGROUND BOX ===
    ctx.fillStyle = "rgba(255,255,255,0.9)";
    ctx.strokeStyle = "black";
    ctx.lineWidth = 1;

    ctx.fillRect(cfg.position_x, cfg.position_y, boxWidth, boxHeight);
    ctx.strokeRect(cfg.position_x, cfg.position_y, boxWidth, boxHeight);

    let cursorY = cfg.position_y + cfg.box_padding + cfg.title_font_size/2;
    let cursorX = cfg.position_x + cfg.box_padding;

    // === DRAW TITLE ===
    ctx.font = `${cfg.title_font_size}px sans-serif`;
    ctx.fillStyle = cfg.font_color === "auto" ? "black" : cfg.font_color;

    ctx.fillText(cfg.title, cursorX, cursorY);
    cursorY += cfg.padding;

    // === DRAW ENTRIES ===
    ctx.font = `${cfg.font_size}px sans-serif`;
    cursorY = cursorY + cfg.title_font_size/2;

    for (const entry of cfg.entries) {
        if (!entry.draw) { // check if draw is defined and false, if undefined or true, draw the entry
            ctx.textAlign = "left";
            let color = entry.color;
            let text = entry.text;
            let text_length = ctx.measureText(text).width;

            // Color square
            let squareSizeX = cfg.font_size + text_length+ cfg.box_padding *2;
            let squareSizeY = cfg.font_size + cfg.box_padding *2;
            let squareX = cfg.position_x + cfg.box_padding;
            let squareY = cursorY + 2;

            ctx.fillStyle = color;
            ctx.fillRect(squareX, squareY, squareSizeX, squareSizeY);
            ctx.strokeRect(squareX, squareY, squareSizeX, squareSizeY);

            // Text
            let textColor = cfg.font_color === "auto"
                ? bestTextColor(color)
                : font_color;

            ctx.fillStyle = textColor;
            ctx.fillText(
                text,
                squareX + cfg.box_padding,
                cursorY + cfg.font_size + cfg.box_padding
            );
        }

        cursorY += rowHeight;
    }
}

// HANDLES THE MOVEMENT OF THE MOUSE OVER THE CANVAS
// USED TO SHOW COORDINATES AND THE HOVER IMAGES
function handleMouseMove(event) {
    const rect = canvas.getBoundingClientRect();
    mouse.x = event.clientX - rect.left;
    mouse.y = event.clientY - rect.top;

    var hasChanged = false;
    var lastBox = hoverBox;

    for (let box of interactiveBoxes) {
        if (
            mouse.x >= box.x && 
            mouse.x <= box.x + box.width &&
            mouse.y >= box.y && 
            mouse.y <= box.y + box.height
        ) {
            // inside the box
            if (lastBox != box) {
                hasChanged = true;
                hoverBox = box;
            }
            break;
        } else { // outside the box
            if (lastBox != null) {
                if (lastBox == box) {
                    hasChanged = true;
                    hoverBox = null; 
                }
            } else {
                hasChanged = false;
                hoverBox = null;
            }
        }
    }

    canvas.style.cursor = hoverBox ? 'pointer' : 'default'; 
    
    
    if (hasChanged) {
        draw(); // Re-render canvas
    }
        

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