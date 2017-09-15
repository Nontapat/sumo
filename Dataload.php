<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Inspector</title>
<!-- Copyright 1998-2017 by Northwoods Software Corporation. -->
<meta charset="UTF-8">
<script src="site/release/go.js"></script>
<link rel='stylesheet' href='dataInspector.css' />
<script src="dataInspector.js"></script>
<script src="site/assets/js/jquery.min.js"></script>




<script id="code">
  function init() {
    if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
    var $ = go.GraphObject.make;  // for conciseness in defining templates

    myDiagram =
      $(go.Diagram, "myDiagramDiv",  // create a Diagram for the DIV HTML element
        {
          "animationManager.isEnabled":false,
          // position the graph in the middle of the diagram
          initialContentAlignment: go.Spot.Center,
          allowDrop: true,
          
          // allow double-click in background to create a new node          
          // enable undo & redo
          "animationManager.duration": 1000,
          "undoManager.isEnabled": true
          
        });

    // These nodes have text surrounded by a rounded rectangle
    // whose fill color is bound to the node data.
    // The user can drag a node by dragging its TextBlock label.
    // Dragging from the Shape will start drawing a new link.

        

    myDiagram.nodeTemplate =
      $(go.Node, "Auto",new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
      //  { locationSpot: go.Spot.Center },
       // new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
        $(go.Panel, "Auto",
        $(go.Shape, "RoundedRectangle",
          { 
            strokeWidth: 3,
            fill: "white", // the default fill, if there is no data-binding
            portId: "", cursor: "pointer",  // the Shape is the port, not the whole Node
            // allow all kinds of links from and to this port

          },
          new go.Binding("fill", "color")),

        $(go.Panel, "Table",
         { defaultAlignment: go.Spot.Left },
          $(go.TextBlock, { row: 0, column: 0, columnSpan: 2, font: "bold 12pt sans-serif" ,margin: 3 },
           new go.Binding("text", "text").makeTwoWay()),
          $(go.TextBlock, { row: 1, column: 0 ,margin: 2}, "Input:"),
          $(go.TextBlock, { row: 1, column: 2 ,margin: 2}, new go.Binding("text", "Input")),
          $(go.TextBlock, { row: 2, column: 0 ,margin: 2}, "Output:"),
          $(go.TextBlock, { row: 2, column: 2 ,margin: 2}, new go.Binding("text", "Output")),

         )
          
       ) );

    // The link shape and arrowhead have their stroke brush data bound to the "color" property
    myDiagram.linkTemplate =
      $(go.Link,
        { toShortLength: 3, 
        relinkableFrom: true, 
        relinkableTo: true,
        routing: go.Link.AvoidsNodes,
        curve: go.Link.JumpOver,
        reshapable: true,
        resegmentable: true,
        corner: 5
        },  // allow the user to relink existing links
        $(go.Shape,
          { isPanelMain: true, strokeWidth: 2 },
          new go.Binding("stroke", "color")),
        $(go.Shape,
          { toArrow: "Standard", stroke: null },
          new go.Binding("fill", "color"))
      );

    // Groups consist of a title in the color given by the group node data
    // above a translucent gray rectangle surrounding the member parts


    // Create the Diagram's Model:
    var nodeDataArray = [ ];
    var linkDataArray = [ ];
    myDiagram.model = new go.GraphLinksModel(nodeDataArray, linkDataArray);
    // Declare which properties to show and how.
    // By default, all properties on the model data objects are shown unless the inspector option "includesOwnProperties" is set to false.

    // Show the primary selection's data, or blanks if no Part is selected:
    var inspector = new Inspector('myInspectorDiv', myDiagram,
      {
        // uncomment this line to only inspect the named 
        // properties below instead of all properties on each object:

        // includesOwnProperties: false,
        properties: {
          "text": {readOnly: true, show: Inspector.showIfPresent },
          // an example of specifying the <input> type
        //  "password": { show: Inspector.showIfPresent, type: 'password' },
          // key would be automatically added for nodes, but we want to 
          //declare it read-only also:
           "key": { readOnly: true, show: Inspector.showIfPresent },
          // color would be automatically added for nodes, but we want to 
          //declare it a color also:
          "color": { readOnly: true, show: Inspector.showIfPresent, type: 'color' },
          // Comments and LinkComments are not in any node or link data (yet), so we add them here:
         
          "UnitPerHour": { readOnly: true, show: Inspector.showIfNode  },
          "CostPerUnit": { readOnly: true, show: Inspector.showIfNode  },
          "Input": { readOnly: true,show: Inspector.showIfNode  },
          "Output": { readOnly: true,show: Inspector.showIfNode  },
         
         // "flag": { show: Inspector.showIfNode, type: 'boolean', defaultValue: true  },
          "LinkComments": { show: Inspector.showIfLink },
          // "isGroup": { readOnly: true, show: Inspector.showIfPresent }
        }
      });
    
  }
var outputfile
  function load() {

      jQuery.getJSON(outputfile, loadjson);

    }

    function loadjson(jsondata) {
    // create the model from the data in the JavaScript object parsed from JSON text
    myDiagram.model = new go.GraphLinksModel(jsondata["nodeDataArray"], jsondata["linkDataArray"]);
  }

  function getFile(filePath) {
    return filePath.substr(filePath.lastIndexOf('\\') + 1);
}

function getoutput() {
  
    outputfile = getFile(inputfile.value);
    jQuery.getJSON(outputfile, loadjson);
}

</script>
</head>
<body onload="init()">
  <div id="sample">
    <span style="display: inline-block; vertical-align: top;">
      <div style="margin-left: 10px;">
        <div id="myDiagramDiv" style="border: solid 1px black; width:700px; height:700px;"></div>
      </div>
    </span>
    <span style="display: inline-block; vertical-align: top;">
      Selected Part:<br/>
      <div id="myInspectorDiv" class="inspector"> </div><br/>
      
    </span>

    <p id="demo"></p>

  <!-- <button onclick="load()">Load</button> -->
  <input id='inputfile' type='file' name='inputfile' onChange='getoutput()'>

  </div>
</body>
</html>
