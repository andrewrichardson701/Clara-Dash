<div id="canvas-wrapper-outer">
    <h1 id="page-header"></h1>
    <div id="canvas-wrapper" style="position: relative; display: inline-block; max-width:min-content">
        <canvas id="canvas" width="1800" height="900" style="border:1px solid black">Your browser does not support HTML canvases.</canvas>
        <div id="coords" style="position: absolute; top: 10px; left: 10px; font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;" hidden></div>
        <div id="timestamp" style="position: absolute; top: 10px; right: 10px; font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;" hidden></div>
        <div id="dimensions" style="font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc;" hidden></div>
    </div>
    <div id="config" style="font-family: monospace; background: rgba(255,255,255,0.8); padding: 4px; border: 1px solid #ccc; margin-top: 10px; max-width:min-content" hidden>
    </div>
</div>

<!-- Canvas JS file for editing the cavnas -->
<script src="js/canvas.js"></script>