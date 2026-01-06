<details>
	<summary>Table of Contents</summary>
	<ol>
		<li><a href="#about-the-project">About The Project</a></li>
		<li>
			<a href="#config">Config</a>
			<ul>
				<li><a href="#example-config">Example Config</a></li>
				<li><a href="#config-breakdown">Config Breakdown</a></li>
				<li><a href="#value-math">Value Math</a></li>
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
		"canvas_id": "template-map-power",
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
			"dimension_x": "auto",
			"dimension_y": "auto",
			"style": {
				"line_width": 2,
				"line_color": "black",
				"font": "monospace",
				"font_size": "auto",
				"font_color": "blue",
                "padding": 5
			},
			"data": {
				"header": null,
				"value": "localhost.ports[0].ifInOctets_rate",
				"value_math": "*8/1000",
				"value_float_num": 2,
				"unit": "kbps",
				"type": "data",
				"threshold_value": "( localhost.ports[0].ifInOctets_rate + localhost.ports[0].ifOutOctets_rate )",
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
				"font_color": "auto",
                "padding": 5
			},
			"data": {
				"header": null,
				"value": "localhost.ports[0].ifOutOctets_rate",
				"value_math": "*8/1000",
				"value_float_num": 2,
				"unit": "kbps",
				"type": "data",
				"threshold_value": "",
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
				"line_width": 0,
                "line_two_way": true,
                "line_direction": "converge"
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
					"image": "localhost.ports[0].graph.graph_full_url",
                    "draw": false
				},
				{
					"header": null,
					"value": "localhost.ports[0].ifInOctets_rate",
					"value_math": "*8/1000",
					"value_float_num": 2,
					"unit": "kbps",
					"type": "data",
					"url": "localhost.ports[0].graph.graph_full_url",
					"image": "localhost.ports[0].graph.graph_full_url",
                    "draw": false
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
				"line_width": 2,
                "line_two_way": true,
                "line_direction": "converge"
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
					"image": "localhost.ports[0].graph.graph_full_url",
                    "draw": true
				},
				{
					"header": null,
					"value": "localhost.ports[0].ifInOctets_rate",
					"value_math": "*8/1000",
					"value_float_num": 2,
					"unit": "kbps",
					"type": "data",
					"url": "localhost.ports[0].graph.graph_full_url",
					"image": "localhost.ports[0].graph.graph_full_url",
                    "draw": true
				}
			]
		}
	},
	"Keys": {
        "DataThresholds": {
            "title": "Data Thresholds",
            "position_x": 350,
            "position_y": 50,
            "draw": true,
            "font_color": "auto",
            "font_size": 12,
            "title_font_size": 18,
            "padding": 5,
            "box_padding": 10,
            "entries": [
                {"color": "red", "text": "< 1"},
                {"color": "orange", "text": "1 ≤ x < 2"},
                {"color": "green", "text": "2 ≤ x < 10"},
                {"color": "orange", "text": "10 ≤ x ≤ 100"},
                {"color": "red", "text": "> 100"}
            ]
        }
    },
    "Thresholds": {
        "data": [
            {"lower": 0, "upper": 1, "color": "red"},
            {"lower": 1, "upper": 2, "color": "orange"},
            {"lower": 2, "upper": 10, "color": "#1ac44a"},
            {"lower": 10, "upper": 100, "color": "orange"},
            {"lower": 100, "upper": 100000000000000, "color": "red"}
        ],
        "default": [
            {"lower": -999999, "upper": 9999999, "color": "grey"}
        ]
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
		<li><strong>canvas_id</strong> - The id to be used in the canvas element. This is needed to draw the canvas. (e.g. "suite-1-power" / "network-map" etc)</li>
		<li><strong>background_img</strong> - Background image for the canvas (e.g. "/img/suite1.png" or "https://example.com/image.png")</li>
		<li><strong>background_scale</strong> - The scale of the background image, in array [x, y] (e.g. [0.7, 0.2] which would be 0.7 * width and 0.2 * height)</li>
		<li><strong>background_anchor</strong> - The anchor point of the background image (e.g. "top" / "bottom" / "left" / "right" / "center" / "middle")</li>
		<li><strong>data_url</strong> - The URL (or file location) of the input / API data to be used for values in JSON format (e.g. "https://example.com/api.php?query=devices" or "json/data.json" or "http://example.com/api/data.php?hostname=s52&ports=0&sensors=1")</li>
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
		<li><strong>image_width</strong> - The width of the hover images, in pixels (default = 400) (e.g. 400 / 300 / 250)</li>
		<li><strong>image_height</strong> - The height of the hover images, in pixels (default = 200) (e.g. 200 / 150 / 100)</li>
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
			<li><strong>dimension_x</strong> - The X length (width) of the node to be drawn. This can be set to "auto" to allow the box to fit the text size. If the text size is also "auto", the text will become 12px (e.g. 50, "auto")</li>
			<li><strong>dimension_y</strong> - The Y length (height) of the node to be drawn. This can be set to "auto" to allow the box to fit the text size. If the text size is also "auto", the text will become 12px (e.g. 30, "auto")</li>
			<li><strong>"style"</strong>: { ... } - All node styling parameters (as an object - similar to the node 'unique_name' object)</li>
			<ul>
				<li><strong>line_width</strong> - The width of the line to be drawn (e.g. 2)</li>
				<li><strong>line_color</strong> - The color of the line to be drawn (e.g. "black" / "#32a836")</li>
				<li><strong>font</strong> - The font family to be used for the inner text (e.g. "monospace" / "Arial")
				<li><strong>font_size</strong> - The size of the font in pixels or "auto" to fit the text to the box dimensions (e.g. 12 / 16 / "auto")</li></li>
				<li><strong>font_color</strong> - The color of the text to be drawn (e.g. "black" / "#32a836" / "auto")</li>
                <li><strong>padding</strong> - The amount of pixels between the text and the box edge when dimension = "auto" (e.g. "2 / 5 / 10. default: 10)</li>
                <li><strong>anchor</strong> - The anchor point of the node to be drawn, as compass points (e.g. "N / E / S / W / NE / SE / SW / NW / C". default = "NW")</li>
			</ul>
			<li><strong>"data"</strong>: { ... } - All node data parameters (as an object - similar to the node 'unique_name' object)</li>
			<ul>
				<li><strong>header</strong> - Header text to be placed infront of the value in the node (e.g. "Power: " / "Traffic: " / null)</li>
				<li><strong>value</strong> - The value to be written in the node. This can be an array key from the data_url or a fixed value. Math functions are also possible here, to do equations and lookup other Node values. (e.g. "localhost.ports[0].ifInOctets_rate" / 25 / "{RACK1-B}" / "( {RACK1-B} + 2 - {RACK1-A} ) / 2" )</li>
				<li><strong>value_math</strong> - Any math function to be applied to the data to make it readable (e.g. "*8/1000" / "*2" / "/10" / null)</li>
				<li><strong>value_float_num</strong> - The decimal places to be kept if the value is a floating point (e.g. 2 would be 0.01 / 3 would be 0.001 / 4 would be 0.0001 etc)</li>
				<li><strong>unit</strong> - Text to be displayed after the value on the node. Commonly the data unit (e.g. "kbps" / "A" / "kW" etc)</li>
				<li><strong>type</strong> - The type of data, for use when setting thresholds (e.g. "data" / "power_a" / "power_kw" / "temperature")</li>
				<li><strong>threshold_value</strong> - The value to be used for thresholds. This will be used instead of the value field when working out thresholds, if defined. (e.g. "localhost.ports[0].ifInOctets_rate" / 25 / "{RACK1-B}" / "( {RACK1-B} + 2 - {RACK1-A} ) / 2" )</li>
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
                <li><strong>line_two_way</strong> - Determine whether the line is one way, or two way (true / false)</li>
                <li><strong>line_direction</strong> - Determine the direction of the link line(s) (e.g. "converge", "diverge", "reverse". default: converge)</li>
			</ul>
            <li><strong>"node_style"</strong>: { ... } - All node styling parameters (as an object - similar to the node 'unique_name' object)</li>
            <ul>
                <li><strong>line_width</strong> - The width of the line to be drawn (e.g. 2)</li>
				<li><strong>line_color</strong> - The color of the line to be drawn (e.g. "black" / "#32a836")</li>
				<li><strong>font</strong> - The font family to be used for the inner text (e.g. "monospace" / "Arial")
				<li><strong>font_size</strong> - The size of the font in pixels or "auto" to fit the text to the box dimensions (e.g. 12 / 16 / "auto")</li></li>
				<li><strong>font_color</strong> - The color of the text to be drawn (e.g. "black" / "#32a836" / "auto")</li>
                <li><strong>padding</strong> - The amount of pixels between the text and the box edge when dimension = "auto" (e.g. "2 / 5 / 10. default: 10)</li>
                <li><strong>anchor</strong> - The anchor point of the node to be drawn, as compass points (e.g. "N / E / S / W / NE / SE / SW / NW / C". default = "NW")</li>
            </ul>
			<li><strong>"data"</strong>: { ... }, { ... } - All link data parameters. The more data arrays there are, the more nodes will be drawn along the arrow, equally spaced. (as an object - similar to the node 'unique_name' object)</li>
			<ul>
				<li><strong>header</strong> - Header text to be placed infront of the value in the node (e.g. "Power: " / "Traffic: " / null)</li>
				<li><strong>value</strong> - The value to be written in the node. This can be an array key from the data_url or a fixed value. Math functions are also possible here, to do equations and lookup other Node values. (e.g. "localhost.ports[0].ifInOctets_rate" / 25 / "{RACK1-B}" / "( {RACK1-B} + 2 - {RACK1-A} ) / 2" )</li>
				<li><strong>value_math</strong> - Any math function to be applied to the data to make it readable (e.g. "*8/1000" / "*2" / "/10" / null)</li>
				<li><strong>value_float_num</strong> - The decimal places to be kept if the value is a floating point (e.g. 2 would be 0.01 / 3 would be 0.001 / 4 would be 0.0001 etc)</li>
				<li><strong>unit</strong> - Text to be displayed after the value on the node. Commonly the data unit (e.g. "kbps" / "A" / "kW" etc)</li>
				<li><strong>type</strong> - The type of data, for use when setting thresholds (e.g. "data" / "power_a" / "power_kw" / "temperature")</li>
				<li><strong>url</strong> - The link URL to be opened on click. This can also be an array key path (e.g. "localhost.ports[0].graph.graph_full_url" / "['host.name.local'].ports[0].graph.graph_full_url" / "https://url.example.com/")</li>
				<li><strong>image</strong> - The image to be shown as a tooltip when hovering over the node. This can be a file path, image url, or array key path (e.g. "localhost.ports[0].graph.graph_full_url" / "https://url.example.com/" / "img/file.png")</li>
			</ul>
		</ul>
	</ul>
	<li><strong>"Keys"</strong>: { ... } - Keys / Legends to be drawn on the screen, showing data thresholds.</li>
	<ul>
		<li><strong>[unique name]</strong>: { ... } - Each Key needs to be an object (an array that allows arrays within it) that has a unique name before it (see <a href="#example-config">example</a>). (e.g. "PowerThresholds": { ... })</li>
		<ul>
			<li><strong>title</strong> - The title to be displayed on the key/legend.</li>
			<li><strong>position_x</strong> - The X coordinate of the key location (anchored from the top left of the key) (e.g. 200)</li>
			<li><strong>position_y</strong> - The Y coordinate of the key location (anchored from the top left of the key) (e.g. 50)</li>
			<li><strong>draw</strong> - Toggle whether the link should be drawn on the canvas. if not included it will default to true. (true / false)</li>
			<li><strong>font_color</strong> - The color of the text to be drawn (e.g. "black" / "#32a836" / "auto")</li>
			<li><strong>font_size</strong> - The size of the font in pixels or "auto" to fit the text to the box dimensions (e.g. 12 / 16 / "auto")</li>
			<li><strong>title_font_size</strong> - The size of the title font in pixels or "auto" to fit the text to the box dimensions (e.g. 12 / 16 / "auto")</li>
			<li><strong>padding</strong> - The padding around the key in pixels (e.g. 12 / 16 / 0)</li>
			<li><strong>box_padding</strong> - The padding around the individual entries in the key in pixels (e.g. 12 / 16 / 0)</li>
			<li><strong>"entries"</strong>: [ {...}, {...} ] - All individual entries for the Key to display.<br>
				Format:  <br>
				{"<strong>color</strong>": "<strong>[color name / hex code]</strong>",<br>
				"<strong>text</strong>": <strong>"string to be shown"</strong>}<br>
				Example:<br>
				<strong>{"color": "red", "text": "< 2A"},<br>
				{"color": "green", "text": "2A ≤ x < 14A"},<br>
				{"color": "red", "text": "> 14A"}</strong>
			</li>
		</ul>
	</ul>
	<li><strong>"Thresholds"</strong>: { ... } - Thresholds for coloring nodes.</li>
	<ul>
		<li><strong>[unique name]</strong>: [ {...}, {...} ] - Each Threshold needs to be an object (an array that allows arrays within it) that has a unique name before it (see <a href="#example-config">example</a>). (e.g. "power_threshold": [ {...}, {...} ])<br>
				Format:  <br>
				{"<strong>lower</strong>": <strong>lower threshold value</strong>,<br>
				"<strong>uppper</strong>": <strong>upper threshold value</strong>,
				"<strong>color</strong>": <strong>color to be displayed</strong>}<br>
				Example:<br>
				<strong>{"lower": 0, "upper": 0.2, "color": "red"},<br>
				{"lower": 0.2, "upper": 2, "color": "orange"},<br>
				{"lower": 2, "upper": 12, "color": "#1ac44a"},<br>
				{"lower": 12, "upper": 13, "color": "orange"},<br>
				{"lower": 13, "upper": 9999, "color": "red"}</strong>
		</li>
	</ul>
</ol>
</details>

<h3>Array Keys</h3>
Array keys can be used in some fields to pull data from the API response. These include 'value', 'image' and 'url' fields.

These can be navigated in one of the below ways:

<ol>
	<li>Syntax: <strong>"hostname.Key1[sequential key].Key2.Key3"</strong> <br>
		Example: <strong>"testhost.ports[0].graph.graph_full_url"</strong> <br>
		This works best for simple hostnames with no domain.</li>
	<li>Syntax: <strong>"['hostname.example.local'].Key1[sequential key].Key2.Key3"</strong> <br>
		Example: <strong>"['testhost.domain.local'].ports[0].graph.graph_full_url"</strong> <br>
		This works best for fully qualified domain anems (FQDNs).</li>
</ol>

<h3>Value Math</h3>
Math equations can be performed in the value fields for data (e.g. Nodes.RACK1-B.data.value or Links.RACK1-B_RACK1-A.data.value). This is extremely useful when trying to get an average or sum together the data in a collection of racks.

This can be done in a number of ways:

<ol>
	<li>Simple performing the maths equation for static data:<br><strong>"2 + 5 - (4 * 6) / 2"</strong></li>
	<li>Using the input data sources and performing the equation on these:<br><strong>"( localhost.ports[0].ifInOctets_rate + localhost.ports[0].ifInOctets_rate + localhost.ports[0].ifInOctets_rate + localhost.ports[0].ifInOctets_rate ) / 2"</strong></li>
	<li>Using the NODE data, which calculates the NODE data output based on the value and value_math. Each node's data should be references in curly brackets "{NODE}":<br><strong>"( {RACK1-A} + {RACK1-B} + {RACK2-A} + {RACK2-B} ) / 2"</strong><br><br>
	This means that instead of having something like this: <br>
<strong>"( localhost.ports[0].ifInOctets_rate + localhost.ports[0].ifInOctets_rate + localhost.ports[0].ifInOctets_rate + localhost.ports[0].ifInOctets_rate ) / 2"</strong>,<br>
you have something more readable, like this: <br>
<strong>"( {RACK1-A} + {RACK1-B} + {RACK2-A} + {RACK2-B} ) / 2"</strong>.</li>