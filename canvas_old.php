<div id="canvas-wrapper" style="position: relative; display: inline-block">
    <canvas id="canvas" width="1800" height="900" style="border:1px solid black">Your browser does not support HTML canvases.</canvas>
    <div id="coords" style="position: absolute; top: 10px; left: 10px; font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;"></div>
    <div id="timestamp" style="position: absolute; top: 10px; right: 10px; font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;"></div>
</div>


<script>

    // ------------------------------------ //
    // =      CONFIGURATION VARIABLES     = //
    // ------------------------------------ //

    var background_img = 'img/21.png';
    var data_url = 'https://observe.ajrich.co.uk/api/data.php'; // json data url - api request or similar
    var enable_periodic_update = true; // refresh the data every minute and reload the page after 30 minutes
    var fixed_canvas_size = false; // this overwrites the canvas_width and canvas_height variables if set to false
    var canvas_width = 1800;
    var canvas_height = 900;


    // ======================================================================
    // ======================================================================
    

    // ------------------------------------------------- //
    // =  USER INPUT ALLOWED IN THE layout() FUNCTION  = //
    // ------------------------------------------------- //

    function racks1_9(json) {
        // rack 1
        drawSensor(ctx, [92,370], [50,30], { lineWidth: 2, strokeStyle: 'black' }, {'header': null, 'value': (json.localhost.ports[0].ifInOctets_rate*8/1000).toFixed(2),   'unit': 'kbps', 'type': 'data', 'url': json.localhost.ports[0].graph.graph_full_url, 'image': json.localhost.ports[0].graph.graph_full_url});
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
        
        racks1_9(json);
        racks10_18(json);
        racks19_27(json);
        racks28_35(json);
        wiring_rack(json);
    }



    // ======================================================================
    // ======================================================================




    // -------------------------------------------- //
    // =        BELOW DRAWS TO THE CANVAS         = //
    // =     ADD LINES TO THE layout() FUNCTION   = //
    // -------------------------------------------- //

    function draw() {
        ctx.clearRect(0, 0, canvas_width, canvas_height);
        ctx.drawImage(background,0,0);   

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

    // SETUP THE CANVAS OBJECT
    var canvas = document.getElementById('canvas');
    var ctx = canvas.getContext("2d");
    var background = new Image();
    background.src = background_img;

    // SET DEFAULT VALUES TO BE OVERWRITTEN LATER
    let interactiveBoxes = [];
    let mouse = { x: 0, y: 0 };
    let hoverBox = null;
    let grid = { width:  0, height: 0 }
    var refresh_counter = 0;

    // ONLOAD, BUILD THE CANVAS - THIS STARTS EVERYTHING
    background.onload = function(){
        // set the canvas size if fixed_canvas_size is not enabled;
        if (!fixed_canvas_size) {
            canvas.height = canvas_height = background.height;
            canvas.width = canvas_width = background.width;
        }
        build();
    }

    // BUILD THE CANVAS - SEPERATED SO THAT IT CAN BE RE-RUN TO REFRESH DATA
    function build() {
        (async() => {
            json = await getInputData();
            
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
    //          {'header': null, 'value': '0.1', 'unit': 'A', 'type': 'power_amps', 'url': 'https://example.com', 'image': 'img/21.png'}
    //  );
    function drawSensor(ctx, coordinates = [0,0], dimensions = [20,10], params = {}, data = {}) {
        var fillText = '';
        var fillColor = 'white'; // default background color

        // Build text
        if (data.header) {
            fillText += data.header;
        }
        if (data.value) {
            if (fillText.length > 0) {
                fillText += ': ';
            }
            fillText += data.value;

            if (data.type) {
                var thold = thresholds(data.value, data.type);
                if (data.value > thold.upper || data.value < thold.lower) {
                    fillColor = 'red';
                } else {
                    fillColor = '#1ac44a'; // green
                }
            }
        }
        if (data.unit) {
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
        var u_thold = 0;
        var l_thold = 0;
        switch (type) {
            case 'power_amps':
                u_thold = 32;
                l_thold = 0.5;
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

        return {'upper' : u_thold, 'lower': l_thold};
    }

    // FETCH THE API DATA FOR THE PAGE
    async function getInputData() {
        try {
            const response = await fetch(data_url);
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Fetch error:', error);
        }
    }

    // PERIDOICALLY RE-RUN THE build() FUNCTION TO RE-LOAD THE CANVAS AND DATA
    // WILL ALSO REFRESH THE PAGE AFTER 30 MINS
    async function periodicUpdate() {
        if (enable_periodic_update) {  
            // refresh counter 
            await sleep(60000); // 1 minute sleep time before getting new data

            // add to counter
            refresh_counter += 1
            // refresh at 30 minutes
            if (refresh_counter === 30) {
                window.location.reload();
            }
            build();
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