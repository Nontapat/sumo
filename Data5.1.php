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
<script src="GoJS-master/FileSaver.min.js"></script>
<?php $conn= mysqli_connect("localhost","root","","sumo"); ?>



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
          "clickCreatingTool.archetypeNodeData": 
          { 
            text: "Process", 
            color: "pink",
            UnitPerHour: "0",
            CostPerUnit: "0",
            Input: "0",
            Output: "0",
            Reject: "0",
            
          
          
          
          },
          
          // enable undo & redo
          "animationManager.duration": 1000,
          "undoManager.isEnabled": true
          
        });

    // These nodes have text surrounded by a rounded rectangle
    // whose fill color is bound to the node data.
    // The user can drag a node by dragging its TextBlock label.
    // Dragging from the Shape will start drawing a new link.


    myDiagram.nodeTemplate =
      $(go.Node, "Auto",
      //  { locationSpot: go.Spot.Center },
       new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
        $(go.Panel, "Auto",
        $(go.Shape, "Rectangle",
          {
            stroke: null, strokeWidth: 0,
            fill: "white", // the default fill, if there is no data-binding
            portId: "", cursor: "pointer",  // the Shape is the port, not the whole Node
            // allow all kinds of links from and to this port
            fromLinkable: true, fromLinkableSelfNode: true, fromLinkableDuplicates: true,
            toLinkable: true, toLinkableSelfNode: true, toLinkableDuplicates: true
          },
          new go.Binding("fill", "color")),
        $(go.TextBlock,
          {
            font: "bold 18px sans-serif",
            stroke: '#111',
            margin: 8,  // make some extra space for the shape around the text
            isMultiline: false,  // don't allow newlines in text
            editable: true  // allow in-place editing by user
          },
          new go.Binding("text", "text").makeTwoWay())
          
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

         includesOwnProperties: false,
        properties: {
          "text": { },
          // an example of specifying the <input> type
        //  "password": { show: Inspector.showIfPresent, type: 'password' },
          // key would be automatically added for nodes, but we want to 
          //declare it read-only also:
           //"key": { readOnly: true, show: Inspector.showIfPresent },
          // color would be automatically added for nodes, but we want to 
          //declare it a color also:
          "color": { show: Inspector.showIfPresent, type: 'color' },
          // Comments and LinkComments are not in any node or link data (yet), so we add them here:
         
          "UnitPerHour": { show: Inspector.showIfNode  },
          "CostPerUnit": { show: Inspector.showIfNode  },
          "Input": { show: Inspector.showIfNode  },
          "Output": { show: Inspector.showIfNode  },
          "Reject": { show: Inspector.showIfNode  },
         "Employee": {type: 'array'},
         "id":{readOnly: true, show: Inspector.showIfPresent},
         // "flag": { show: Inspector.showIfNode, type: 'boolean', defaultValue: true  },
          "LinkComments": { show: Inspector.showIfLink },
          // "isGroup": { readOnly: true, show: Inspector.showIfPresent }
        }
      });


  }

  function save() {
    document.getElementById("mySavedModel").value = myDiagram.model.toJson();
    myDiagram.isModified = false;
		 var blob = new Blob([document.getElementById("mySavedModel").value], {type: "text/plain;charset=utf-8"});
	saveAs(blob, "test.json"); 
	
  }
   function wr(){
	 document.getElementById("mySavedModel").value = myDiagram.model.toJson();
    myDiagram.isModified = false;

    var object = go.Model.fromJson(document.getElementById("mySavedModel").value);  
    var i = "";
    for (i in object.De) {
    console.log(object.De[i].text);
    }
    console.log(object);


	
  }

  function set() {
    document.getElementById("mySavedModel").value = myDiagram.model.toJson();
    myDiagram.isModified = false;
    var y = go.Model.fromJson(document.getElementById("mySavedModel").value);
    var j = "";
    
    
    <?php 
     $sql = "SELECT * FROM datatest WHERE id = '2' " ;
     $result = mysqli_query($conn,$sql); 
     while($row = mysqli_fetch_array($result))
     {
     $name = $row['name'] ;
     $uph = $row['uph'];
     $cpu = $row['cpu'];
     $input = $row['input'];
     $output =$row['output'];
     $reject = $row['reject'];
     $pk = $row['pk'];
     $id = $row['id'];
    }
    ?>
    var name = <?php echo json_encode($name); ?>;
    var uph = <?php echo json_encode($uph); ?>;
    var cpu = <?php echo json_encode($cpu); ?>;
    var input = <?php echo json_encode($input); ?>;
    var output = <?php echo json_encode($output); ?>;
    var reject = <?php echo json_encode($reject); ?>;
    var pk = <?php echo json_encode($pk); ?>;
    var id = <?php echo json_encode($id); ?>;

    for (j in y.De) {
    y.De[j].text = name;
    y.De[j].UnitPerHour = uph;
    y.De[j].CostPerUnit = uph;
    y.De[j].Input = input;
    y.De[j].Output = output;
    y.De[j].Reject = reject;
    y.De[j].key = pk;
    y.De[j].id = id;
     

    }

    myDiagram.model = go.Model.fromJson(y);
    document.getElementById("mySavedModel").value = myDiagram.model.toJson();
    myDiagram.isModified = false;
  }
 
    
  function load() {
    myDiagram.model = go.Model.fromJson(document.getElementById("mySavedModel").value);
  }
  

  function post() {
    document.getElementById("mySavedModel").value = myDiagram.model.toJson();
    myDiagram.isModified = false;

    var name  , uph , cpu , input , output , reject , pk = '';
    var object = go.Model.fromJson(document.getElementById("mySavedModel").value);  
    var i = "";
    for (i in object.De) {
    name = object.De[i].text;
    uph = object.De[i].UnitPerHour;
    cpu = object.De[i].CostPerUnit;
    input = object.De[i].Input;
    output = object.De[i].Output;
    reject = object.De[i].Reject;
    pk = object.De[i].key;
   
    var xmlhttp = new XMLHttpRequest(); 
            xmlhttp.open("POST", "dataadd.php", true);
            

            xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xmlhttp.send("name=" + name + "&uph=" + uph +"&cpu=" + cpu + "&input=" + input + "&output=" +output +  "&reject=" + reject + "&pk=" + pk  );
        
  
        
        
        
        
        
        
        
        
        
        
        
        
        }



        xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  alert(xmlhttp.responseText);
                }
            }
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
</br>
  <button id="SaveButton" onclick="save()">Write</button>
	<button id="SaveButton" onclick="wr()">Save</button>
  <button onclick="load()">Load</button>
  <button onclick="post()">Post</button>
  <button onclick="set()">Set</button>
  </br>
  <textarea id="mySavedModel" style="width:100%;height:300px">
  
{ "class": "go.GraphLinksModel",
  "linkFromPortIdProperty": "fromPort",
  "linkToPortIdProperty": "toPort",
  "nodeDataArray": [
 ],
  "linkDataArray": [
 ]}
  </textarea>

  </div>
</body>
</html>
<script>

</script>
