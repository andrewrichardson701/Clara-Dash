<div id="canvas-wrapper" style="position: relative; display: inline-block">
    <canvas id="canvas" width="1800" height="900" style="border:1px solid black">Your browser does not support HTML canvases.</canvas>
    <div id="coords" style="position: absolute; top: 10px; left: 10px; font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;"></div>
    <div id="timestamp" style="position: absolute; top: 10px; right: 10px; font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;"></div>
</div>


<script>
    // ------------------------------------------------- //
    // =  USER INPUT ALLOWED IN THE layout() FUNCTION  = //
    // ------------------------------------------------- //

    function loopDrawSensors() {
        var sensors = map_json.Sensors;
        sensors.forEach((sensor) => {
            sensor.data.value = resolveArrayPath(json, sensor.data.value);
            drawSensor(
                ctx, // canvas
                [sensor.position_x,sensor.position_y], // coordinates
                [sensor.dimension_x,sensor.dimension_y], // dimensions
                sensor.style, // style
                sensor.data // data
            );
        });
    }

    function racks1_9(json) {
        // rack 1
        drawSensor(ctx, [92,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': json.localhost.ports[0].ifInOctets_rate, 'value_math': '*8/1000', 'value_float_num': 2, 'unit': 'kbps', 'type': 'data', 'url': json.localhost.ports[0].graph.graph_full_url, 'image': json.localhost.ports[0].graph.graph_full_url});
        drawSensor(ctx, [92,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': (json.localhost.ports[0].ifOutOctets_rate*8/1000).toFixed(2),  'unit': 'kbps', 'type': 'data', 'url': json.localhost.ports[0].graph.graph_full_url, 'image': json.localhost.ports[0].graph.graph_full_url});
        // rack 2
        drawSensor(ctx, [166,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [166,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 3
        drawSensor(ctx, [240,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [240,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 4
        drawSensor(ctx, [313,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [313,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 5
        drawSensor(ctx, [387,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [387,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 6
        drawSensor(ctx, [460,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [460,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 7
        drawSensor(ctx, [534,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [534,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 8
        drawSensor(ctx, [608,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [608,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 8
        drawSensor(ctx, [681,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [681,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
    }

    function racks10_18(json) {
        // rack 10
        drawSensor(ctx, [900,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [900,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 11
        drawSensor(ctx, [974,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [974,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 12
        drawSensor(ctx, [1048,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1048,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 13
        drawSensor(ctx, [1120,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1120,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 14
        drawSensor(ctx, [1193,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1193,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 15
        drawSensor(ctx, [1268,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1268,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 16
        drawSensor(ctx, [1341,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1341,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 17
        drawSensor(ctx, [1415,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1415,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 18
        drawSensor(ctx, [1488,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1488,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
    }

    function racks19_27(json) {
        // rack 19
        drawSensor(ctx, [92,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [92,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 20
        drawSensor(ctx, [166,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [166,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 21
        drawSensor(ctx, [240,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [240,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 22
        drawSensor(ctx, [313,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [313,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 23
        drawSensor(ctx, [387,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [387,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 24
        drawSensor(ctx, [460,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [460,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 25
        drawSensor(ctx, [534,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [534,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 26
        drawSensor(ctx, [608,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [608,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 27
        drawSensor(ctx, [681,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [681,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
    }

    function racks28_35(json) {
        // rack 28
        drawSensor(ctx, [974,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [974,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 29
        drawSensor(ctx, [1048,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1048,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 30
        drawSensor(ctx, [1120,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1120,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 31
        drawSensor(ctx, [1193,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1193,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 32
        drawSensor(ctx, [1268,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1268,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 33
        drawSensor(ctx, [1341,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1341,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 34
        drawSensor(ctx, [1415,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1415,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        // rack 35
        drawSensor(ctx, [1488,125], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [1488,175], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
    }

    function wiring_rack(json) {
        // wiring rack
        drawSensor(ctx, [900,110], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '1',   'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
        drawSensor(ctx, [900,143], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});
    }

    function layout() {
        // ------------------------------------ //
        // =     ADD CANVAS OBJECTS BELOW     = //
        // ------------------------------------ //
        // e.g. drawSensor(ctx, [681,420], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'});

        // racks1_9(json);
        // racks10_18(json);
        // racks19_27(json);
        // racks28_35(json);
        // wiring_rack(json);
        loopDrawSensors();
    }



    // ======================================================================
    // ======================================================================




    // -------------------------------------------- //
    // =        BELOW DRAWS TO THE CANVAS         = //
    // =     ADD LINES TO THE layout() FUNCTION   = //
    // -------------------------------------------- //

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
    // ======================================================================




    // -------------------------------------------- //
    // =       DO NOT TOUCH ANY INFO BELOW        = //
    // -------------------------------------------- //

    // ERROR IF A CONFIG WAS NOT LOADED
    if (typeof map_file === 'undefined') {
        document.getElementById('canvas-wrapper').innerText = "No config loaded.";
        throw new FatalError("No config loaded.");
    }

    // INITIALISE THE JSON ARRAYS
    var map_json = null;
    var json     = null;
    
    // SETUP THE CANVAS OBJECT
    var canvas = document.getElementById('canvas');
    var ctx = canvas.getContext("2d");
    var background = new Image();

    // SET DEFAULT VALUES TO BE OVERWRITTEN LATER
    let interactiveBoxes = [];
    let mouse = { x: 0, y: 0 };
    let hoverBox = null;
    let grid = { width:  0, height: 0 }
    var refresh_counter = 0;
    var background_img = 'img/default.png'; // overwritten by map config
    var background_x_pos = 0;
    var background_y_pos = 0;
    var background_x_scale = 1;
    var background_y_scale = 1;

    // CONVER THE CONFIG AND START THE BUILD
    (async () => {
        try {
            // Load map JSON first
            map_json = await getData(map_file);

            // set the data_url
            data_url = map_json.Config.data_url;

            // Determine background from JSON (if available)
            if (map_json && map_json.Config.background_img) {
                background_img = map_json.Config.background_img; // background image from json
            } 

            // Set the background image - defaults to 'img/default.png' if none available
            background.src = background_img; 
            
            // If background scaling set in the config, apply it to the image
            if (map_json.Config.background_scale) {
                background_scale = map_json.Config.background_scale;
                // check if the scale is an array
                if (background_scale instanceof Array) {
                    // check if there are 2 parameters (0,1)
                    if (1 in background_scale) {
                        background_x_scale = background_scale[0];
                        background_y_scale = background_scale[1];
                    } else {
                        background_y_scale = background_y_scale = background_scale[0];
                    }
                } else {
                    background_y_scale = background_y_scale = background_scale;
                }

                // check to make sure they are positive numbers (can be float)
                if (!checkValidNumber(background_x_scale)) {
                    console.log('Background scaling for the x axis is not a valid number. These can be any decimal above 0.');
                    background_x_scale = 100;
                }
                if (!checkValidNumber(background_y_scale)) {
                    console.log('Background scaling for the y axis is not a valid number. These can be any decimal above 0.');
                    background_y_scale = 100;
                }
            }

            // Set canvas size
            if (!map_json.Config.fixed_canvas_size) {
                // if not a fixed sizing, scale to background image (including the scaling)
                canvas.height = canvas_height = background.height*background_y_scale;
                canvas.width = canvas_width = background.width*background_x_scale;
            } else {
                // fixed sizing, get the sizing from the config
                canvas.height = canvas_height = map_json.Config.canvas_height
                canvas.width = canvas_width = map_json.Config.canvas_width;
            }
            
            // check for background posititon in config, adjust anchor point (valid options: center/middle, left, right, top, bottom)
            if (map_json.Config.background_anchor) {
                var background_anchor = map_json.Config.background_anchor;
                switch (background_anchor){
                    case "middle":
                    case "center":
                        background_x_pos = (canvas.width - (background.width*background_x_scale))/2;
                        background_y_pos = (canvas.height - (background.height*background_y_scale))/2;
                        break;
                    case "left":
                        background_x_pos = 0;
                        background_y_pos = (canvas.height - (background.height*background_y_scale))/2;
                        break;
                    case "right":
                        background_x_pos = canvas.width - (background.width*background_x_scale);
                        background_y_pos = (canvas.height - (background.height*background_y_scale))/2;
                        break;
                    case "top":
                        background_x_pos = (canvas.width - (background.width*background_x_scale))/2;
                        background_y_pos = 0;
                        break;
                    case "bottom":
                        background_x_pos = (canvas.width - (background.width*background_x_scale))/2;
                        background_y_pos = canvas.height - (background.height*background_y_scale);
                        break;
                    case "default":
                    case "top-left":
                        background_x_pos = 0;
                        background_y_pos = 0;
                    default:
                        background_x_pos = 0;
                        background_y_pos = 0;
                }
            }

            // Wait for image to load
            background.onload = function() {
                
                // Build the canvas now that both JSON & background are ready
                build();
            };
        } catch (err) {
            console.error("Error loading map or background:", err);
        }
    })();

    

    // BUILD THE CANVAS - SEPERATED SO THAT IT CAN BE RE-RUN TO REFRESH DATA
    function build() {
        (async() => {
            // get the data json
            json = await getData(data_url);

            draw(); // draw the canvas
            updateTimestamp(); // write the tiemstamp
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
    function drawSensor(ctx, coordinates = [0,0], dimensions = [20,10], params = {}, data = {}) {
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
        ctx.lineWidth = params.lineWidth || 1;
        ctx.strokeStyle = params.strokeStyle || 'black';
        ctx.rect(coordinates[0], coordinates[1], dimensions[0], dimensions[1]);
        ctx.stroke();

        // Draw text
        ctx.font = "12px Arial";
        ctx.textAlign = "center"; 
        ctx.textBaseline = "middle";
        ctx.fillStyle = 'black'; // Text color
        ctx.fillText(fillText, coordinates[0] + (dimensions[0] / 2), coordinates[1] + (dimensions[1] / 2));
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

    // ======================================================================
    // ======================================================================

</script>