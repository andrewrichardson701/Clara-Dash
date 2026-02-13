<div id="canvas-wrapper-outer">
    <div class="container">
        <h1 id="page-header"></h1>
    </div>
    <div id="canvas-wrapper" style="position: relative; max-width:min-content; margin-right:auto; margin-left:auto; display:flex" class="well-nopad bg-dark">
        <div style="display: inline-block; position: relative;">
            <canvas id="<?php echo($canvas_id); ?>" width="1800" height="900" style="border:1px solid black; background-color:white">Your browser does not support HTML canvases.</canvas>
            <div id="coords" class="well-nopad" style="position: absolute; top: 10px; left: 10px; font-family: monospace; color:black; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;" hidden></div>
            <div id="timestamp" class="well-nopad" style="position: absolute; top: 10px; right: 10px; font-family: monospace; color:black; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;" hidden></div>
            <div id="dimensions" class="well-nopad" style="font-family: monospace; color:black; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;" hidden></div>
        </div>
    </div>
    <div id="config" class="container well-nopad bg-dark"style="font-family: monospace; padding: 4px; border: 1px solid #ccc; margin-top: 10px; max-width:min-content" hidden>
    </div>
</div>

<!-- <div id="canvas-wrapper-outer">
    <h1 id="page-header"></h1>
    <div id="canvas-wrapper" style="position: relative; display: inline-block; max-width:min-content">
        <canvas id="<?php echo($canvas_id); ?>" width="1800" height="900" style="border:1px solid black">Your browser does not support HTML canvases.</canvas>
        <div id="coords"  style="position: absolute; top: 10px; left: 10px; font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;" hidden></div>
        <div id="timestamp" style="position: absolute; top: 10px; right: 10px; font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;" hidden></div>
        <div id="dimensions" style="font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;" hidden></div>
    </div>
    <div id="config" style="font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc; margin-top: 10px; max-width:min-content" hidden>
    </div>
</div> -->

<script>
    var map_file = '<?php echo($map_file); ?>';
    var canvas_id = '<?php echo($canvas_id); ?>';
</script>

<!-- Canvas JS file for editing the cavnas -->
<script src="js/canvas.js"></script>