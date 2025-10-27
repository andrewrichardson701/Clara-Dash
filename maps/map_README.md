<details>
	<summary>Table of Contents</summary>
	<ol>
		<li><a href="#about-the-project">About The Project</a></li>
		<li>
			<a href="#config">Config</a>
			<ul>
				<li><a href="#example-config">Example Config</a></li>
				<li><a href="#config-breakdown">Config Breakdown</a></li>
			</ul>
		</li>
	</ol>
</details>

<!-- ABOUT THE PROJECT -->
<h2>About The Project</h2>
The aim of this project is to add a third party API to observium and allow php-weathermap style weathermaps to be created using JS and a HTML canvas.

<h3>Built With</h3>
* [![PHP][PHP.net]][PHP-url]
* [![JavaScript][JavaScript.com]][JavaScript-url]


<!-- CONFIG-->
<h2>Config</h2>

The config files are saves in "api/maps/" as json files.
These setup the canvas settings, all of the nodes and all of the links between nodes

<h3>Example Config</h3>

<details>
  <summary>
  Here is the template config included in the package:
  </summary>
  
<pre>
{
	"Config": {
		"page_title": "Template",
		"page_header": "Suite Template Power Usage",
		"background_img": "img/default.png",
		"background_scale": [0.5, 0.5],
		"background_anchor": "default",
		"data_url": "template_data.json",
		"enable_periodic_update": true,
		"fixed_canvas_size": true,
		"canvas_width": 400,
		"canvas_height": 300,
		"show_grid" : false,
		"show_page_header": true,
		"show_coordinates": true,
		"show_timestamp": true,	
		"show_dimensions": true,
		"show_config": true
	},
	"Nodes": {
		"RACK1-A": {
			"name": "Rack 1 Feed A",
			"draw" : true,
			"position_x": 200,
			"position_y": 50,
			"dimension_x": 50,
			"dimension_y": 30,
			"style": {
				"line_width": 2,
				"line_color": "black",
				"font": "monospace",
				"font_size": "auto",
				"font_color": "blue"
			},
			"data": {
				"header": null,
				"value": "localhost.ports[0].ifInOctets_rate",
				"value_math": "*8/1000",
				"value_float_num": 2,
				"unit": "kbps",
				"type": "data",
				"url": "localhost.ports[0].graph.graph_full_url",
				"image": "localhost.ports[0].graph.graph_full_url"
			}
		},
		"RACK1-B": {
			"name": "Rack 1 Feed B",
			"draw": true,
			"position_x": 200,
			"position_y": 200,
			"dimension_x": 80,
			"dimension_y": 30,
			"style": {
				"line_width": 2,
				"line_color": "black",
				"font": "Arial",
				"font_size": 12,
				"font_color": "auto"
			},
			"data": {
				"header": null,
				"value": "localhost.ports[0].ifOutOctets_rate",
				"value_math": "*8/1000",
				"value_float_num": 2,
				"unit": "kbps",
				"type": "data",
				"url": "localhost.ports[0].graph.graph_full_url",
				"image": "localhost.ports[0].graph.graph_full_url"
			}
		}
	},
	"Links": {
		"RACK1-A_RACK1-B": {
			"nodes": [
				{"node": "RACK1-A", "anchor": "E", "offset": [0,0]},
				{"node": "RACK1-B", "anchor": "E", "offset": [0,0]}
			],
			"draw": true,
			"style": {
				"color": "purple",
				"width": 10,
				"line_color": "black",
				"line_width": 0
			},
			"data": [
				{
					"header": null,
					"value": "localhost.ports[0].ifInOctets_rate",
					"value_math": "*8/1000",
					"value_float_num": 2,
					"unit": "kbps",
					"type": "data",
					"url": "localhost.ports[0].graph.graph_full_url",
					"image": "localhost.ports[0].graph.graph_full_url"
				},
				{
					"header": null,
					"value": "localhost.ports[0].ifInOctets_rate",
					"value_math": "*8/1000",
					"value_float_num": 2,
					"unit": "kbps",
					"type": "data",
					"url": "localhost.ports[0].graph.graph_full_url",
					"image": "localhost.ports[0].graph.graph_full_url"
				}
			]
		},
		"RACK1-B_RACK1-A": {
			"nodes": [
				{"node": "RACK1-B", "anchor": "W", "offset": [0,0]},
				{"node": "RACK1-A", "anchor": "W", "offset": [0,0]}
			],
			"draw": true,
			"style": {
				"color": "orange",
				"width": 5,
				"line_color": "black",
				"line_width": 2
			},
			"data": [
				{
					"header": null,
					"value": "localhost.ports[0].ifInOctets_rate",
					"value_math": "*8/1000",
					"value_float_num": 2,
					"unit": "kbps",
					"type": "data",
					"url": "localhost.ports[0].graph.graph_full_url",
					"image": "localhost.ports[0].graph.graph_full_url"
				},
				{
					"header": null,
					"value": "localhost.ports[0].ifInOctets_rate",
					"value_math": "*8/1000",
					"value_float_num": 2,
					"unit": "kbps",
					"type": "data",
					"url": "localhost.ports[0].graph.graph_full_url",
					"image": "localhost.ports[0].graph.graph_full_url"
				}
			]
		}
	}
}
</pre>
</details>

<h3>Config Breakdown</h3>

<details>
<summary>The config settings can be configured in different ways. Some entries are not required, but rule of thumb is to include all entries and leave the = <strong>null</strong> if not needed.</summary>

<ol>
	<li><strong>"Config"</strong> : { ... } - General config options for the canvas and page.</li>
	<ul>
		<li><strong>page_title</strong> - The title of the page to be shown on the browser tab (e.g. "Suite 1 - Temperature")</li>
		<li><strong>page_header</strong> - The header to be shown on the page (e.g. "Suite 1 Temperature")</li>
		<li><strong>background_img</strong> - Background image for the canvas (e.g. "/img/suite1.png" or "https://example.com/image.png")</li>
		<li><strong>background_scale</strong> - The scale of the background image, in array [x, y] (e.g. [0.7, 0.2] which would be 0.7 * width and 0.2 * height)</li>
		<li><strong>background_anchor</strong> - The anchor point of the background image (e.g. "top" / "bottom" / "left" / "right" / "center" / "middle")</li>
		<li><strong>data_url</strong> - The URL (or file location) of the input / API data to be used for values in JSON format (e.g. "https://example.com/api.php?query=devices" or "json/data.json")</li>
		<li><strong>enable_periodic_update</strong> - Refreshes the canvas every 1 minute with new data. The page doesnt reload, but the content of the canvas is re-drawn (true / false)</li>
		<li><strong>fixed_canvas_size</strong> - Make the canvas size the same as the <strong> canvas_width </strong> and <strong>  canvas_height</strong>, otherwise the canvas will auto-size to the background image (true / false)</li>
		<li><strong>show_grid</strong> - Show a grid on the canvas, set 10 pixels apart. (true / false)</li>
		<li><strong>canvas_width</strong> - The width of the canvas in pixels (e.g. 950)</li>
		<li><strong>canvas_height</strong> - The height of the canvas in pixels (e.g. 520)</li>
		<li><strong>show_page_header</strong> - Shows the <strong>page_header</strong> above the canvas (true / false)</li>
		<li><strong>show_coordinates</strong> - Shows the coordinates overlay in the top left corner (true / false)</li>
		<li><strong>show_timestamp</strong> - Shows the canvas draw timestamp in the top right corner (true / false)</li>
		<li><strong>show_dimensions</strong> - Shows the dimensions of the canvas below the canvas element (true / false)</li>
		<li><strong>show_config</strong> - Shows the config file contents below the canvas (and dimensions if enabled) in a pre element (true / false)</li>
	</ul>
	<br>
	<li><strong>"Nodes"</strong>: { ... } - Config for each node to be drawn on the canvas.</li>
	<ul>
		<li><strong>[unique name]</strong>: { ... } - Each node needs to be an object (an array that allows arrays within it) that has a unique name before it (see <a href="#example-config">example</a>) to be referenced at a later date (e.g. "RACK1-POWERA": { ... })</li>
		<ul>
			<li><strong>name</strong> - A friendly readable name for the node (e.g. "Rack 1 Power feed A")</li>
			<li><strong>draw</strong> - Toggle whether the node should be drawn on the canvas. if not included it will default to true. (true / false)</li>
			<li><strong>position_x</strong> - The X coordinate of the node location (anchored from the top left of the node) (e.g. 200)</li>
			<li><strong>position_y</strong> - The Y coordinate of the node location (anchored from the top left of the node) (e.g. 50)</li>
			<li><strong>dimension_x</strong> - The X length (width) of the node to be drawn (e.g. 50)</li>
			<li><strong>dimension_y</strong> - The Y length (height) of the node to be drawn (e.g. 30)</li>
			<li><strong>"style"</strong>: { ... } - All node styling parameters (as an object - similar to the node 'unique_name' object)</li>
			<ul>
				<li><strong>line_width</strong> - The width of the line to be drawn (e.g. 2)</li>
				<li><strong>line_color</strong> - The color of the line to be drawn (e.g. "black" / "#32a836")</li>
				<li><strong>font</strong> - The font family to be used for the inner text (e.g. "monospace" / "Arial")
				<li><strong>font_size</strong> - The size of the font in pixels or "auto" to fit the text to the box dimensions (e.g. 12 / 16 / "auto")</li></li>
				<li><strong>font_color</strong> - The color of the text to be drawn (e.g. "black" / "#32a836" / "auto")</li>
			</ul>
			<li><strong>"data"</strong>: { ... } - All node data parameters (as an object - similar to the node 'unique_name' object)</li>
			<ul>
				<li><strong>header</strong> - Header text to be placed infront of the value in the node (e.g. "Power: " / "Traffic: " / null)</li>
				<li><strong>value</strong> - The value to be written in the node. This can be an array key from the data_url or a fixed value (e.g. "localhost.ports[0].ifInOctets_rate" / 25)</li>
				<li><strong>value_math</strong> - Any math function to be applied to the data to make it readable (e.g. "*8/1000" / "*2" / "/10" / null)</li>
				<li><strong>value_float_num</strong> - The decimal places to be kept if the value is a floating point (e.g. 2 would be 0.01 / 3 would be 0.001 / 4 would be 0.0001 etc)</li>
				<li><strong>unit</strong> - Text to be displayed after the value on the node. Commonly the data unit (e.g. "kbps" / "A" / "kW" etc)</li>
				<li><strong>type</strong> - The type of data, for use when setting thresholds (e.g. "data" / "power_a" / "power_kw" / "temperature")</li>
				<li><strong>url</strong> - The link URL to be opened on click. This can also be an array key path (e.g. "localhost.ports[0].graph.graph_full_url" / "https://url.example.com/")</li>
				<li><strong>image</strong> - The image to be shown as a tooltip when hovering over the node. This can be a file path, image url, or array key path (e.g. "localhost.ports[0].graph.graph_full_url" / "https://url.example.com/" / "img/file.png")</li>
			</ul>
		</ul>
	</ul>
	<br>
	<li><strong>"Links"</strong>: { ... } - Config for each link to be drawn on the canvas.</li>
	<ul>
		<li><strong>[unique name]</strong>: { ... } - Each link needs to be an object (an array that allows arrays within it) that has a unique name before it (see <a href="#example-config">example</a>) to be referenced at a later date (e.g. "RACK1-POWERA_RACK2-POWERB": { ... })</li>
		<ul>
			<li><strong>nodes</strong>: [ { ... }, { ... } ] - The 2x nodes to draw the line between, as 2x objects.<br>
				Format:  <br>
				{"<strong>node</strong>": "<strong>[unique_name]</strong>", "<strong>anchor</strong>": "[N / E / S / W / NE / SE / SW / NW / C]", "<strong>offset</strong>": [x, y]}</li>					
			<ul>
			<li><strong>node</strong> - The <strong>[unique name]</strong> of the node to link to/from (e.g. "RACK1-POWERA")</li>
			<li><strong>anchor</strong> - The anchor point of the node, as compass points (e.g. "N / E / S / W / NE / SE / SW / NW / C")</li>
			<li><strong>offset</strong>: [... , ...] - The offset of the anchor point in pixels, as an array in format [x offset, y offset] (e.g. [10, 2] / [0, 0] / [12, 4])</li>
			<li>Working example: <br>
				{"<strong>node</strong>": "RACK1_A", "<strong>anchor</strong>": "E", "<strong>offset</strong>": [10, 20]},<br>
				{"<strong>node</strong>": "RACK2_B", "<strong>anchor</strong>": "E", "<strong>offset</strong>": [0, 0]}</li>
			</ul>
			<li><strong>draw</strong> - Toggle whether the link should be drawn on the canvas. if not included it will default to true. (true / false)</li>
			<li><strong>"style"</strong>: { ... } - All node styling parameters (as an object - similar to the node 'unique_name' object)</li>
			<ul>
				<li><strong>color</strong> - The inner color of the arrow to be drawn (e.g. "white" / "orange" / "#32a836")</li>
				<li><strong>width</strong> - The width of the arrow to be drawn (e.g. 2 / 5 / 10)</li>
				<li><strong>line_color</strong> - The color of the outer line to be drawn around the arrow (e.g. "black" / "#32a836")</li>
				<li><strong>line_width</strong> - The width of the outer line to be drawn around the arrow (e.g. 2)</li>
			</ul>
			<li><strong>"data"</strong>: { ... }, { ... } - All link data parameters. The more data arrays there are, the more nodes will be drawn along the arrow, equally spaced. (as an object - similar to the node 'unique_name' object)</li>
			<ul>
				<li><strong>header</strong> - Header text to be placed infront of the value in the node (e.g. "Power: " / "Traffic: " / null)</li>
				<li><strong>value</strong> - The value to be written in the node. This can be an array key from the data_url or a fixed value (e.g. "localhost.ports[0].ifInOctets_rate" / 25)</li>
				<li><strong>value_math</strong> - Any math function to be applied to the data to make it readable (e.g. "*8/1000" / "*2" / "/10" / null)</li>
				<li><strong>value_float_num</strong> - The decimal places to be kept if the value is a floating point (e.g. 2 would be 0.01 / 3 would be 0.001 / 4 would be 0.0001 etc)</li>
				<li><strong>unit</strong> - Text to be displayed after the value on the node. Commonly the data unit (e.g. "kbps" / "A" / "kW" etc)</li>
				<li><strong>type</strong> - The type of data, for use when setting thresholds (e.g. "data" / "power_a" / "power_kw" / "temperature")</li>
				<li><strong>url</strong> - The link URL to be opened on click. This can also be an array key path (e.g. "localhost.ports[0].graph.graph_full_url" / "https://url.example.com/")</li>
				<li><strong>image</strong> - The image to be shown as a tooltip when hovering over the node. This can be a file path, image url, or array key path (e.g. "localhost.ports[0].graph.graph_full_url" / "https://url.example.com/" / "img/file.png")</li>
			</ul>
		</ul>
	</ul>
</ol>
</details>