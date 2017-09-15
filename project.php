<!DOCTYPE html>
<html>
<head>
<!-- <?php //include("navbar.html"); ?> -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SUMO</title>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<script src="site/release/go.js"></script>
<link rel='stylesheet' href='css/DataInspector.css'/>
<script src="dataInspector.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script id="code">
  function init() {
    var $ = go.GraphObject.make;  // for conciseness in defining templates
    myDiagram =
      $(go.Diagram, "myDiagramDiv",  // create a Diagram for the DIV HTML element
        {
          "animationManager.isEnabled":false,
          // position the graph in the middle of the diagram
          initialContentAlignment: go.Spot.Center,
          allowDrop: true,
          
          // allow double-click in background to create a new node
          "clickCreatingTool.archetypeNodeData": { text: "Process", color: "pink" },
          
          // enable undo & redo
          "animationManager.duration": 1000,
          "undoManager.isEnabled": true
          
        });

    myDiagram.nodeTemplate =
      $(go.Node, "Auto",
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

    // Create the Diagram's Model:
    var nodeDataArray = [ ];
    var linkDataArray = [ ];
    myDiagram.model = new go.GraphLinksModel(nodeDataArray, linkDataArray);

    // Show the primary selection's data, or blanks if no Part is selected:
    var inspector = new Inspector('myInspectorDiv', myDiagram,
      {
        properties: {
          "key": { readOnly: true, show: Inspector.showIfPresent },
          "text": { },
          "color": { show: Inspector.showIfPresent, type: 'color' },
          //"UnitPerHour": { show: Inspector.showIfNode  },
          //"CostPerUnit": { show: Inspector.showIfNode  },
          "LinkComments": { show: Inspector.showIfLink },
        }
      });
  }

  function save() {
    document.getElementById("mySavedModel").value = myDiagram.model.toJson();
    myDiagram.isModified = false;
  }
  function load() {
    myDiagram.model = go.Model.fromJson(document.getElementById("mySavedModel").value);
  }

// function checkbox(){
//   var checkboxes = document.getElementsByName('Emp');
//   var checkboxesChecked = [];
//   // loop over them all
//   for (var i=0; i<checkboxes.length; i++) {
//      // And stick the checked ones onto an array...
//      if (checkboxes[i].checked) {
//         checkboxesChecked.push(checkboxes[i].value);
//      }
//   }
//   document.getElementById("show").value = checkboxesChecked;
// }

function radiocheck(procID,uph,numOfhour,costperProc){
  var procchecked = document.getElementsByName('proc');
  var txt = "";

   //loop over them all
  for (var i=0; i<procchecked.length; i++) {
 // And stick the checked ones onto an array...
    if (procchecked[i].checked) {
        txt = txt+procchecked[i].value;
 
     }
  }
   document.getElementById("showProcess").value = txt;
   document.getElementById("keyProc").value = procID;
   document.getElementById("showuph").value = uph;
   document.getElementById("shownumOfhour").value = numOfhour;
   document.getElementById("showcostperProc").value = costperProc;
}
  $(document).ready(function() {
    $('input:button').click(function() {
        var intId = $("#buildyourform div").length + 1;
        var fieldWrapper = $("<div class=\"fieldwrapper\" id=\"field" + intId + "\"/>");
        //var fName = $("<input type=\"text\" class=\"fieldname\" placeholder=\" \" />");
        var empGoal = $("<input type=\"text\" class=\"fieldname\" name=\"empGoal[]\" />");
        var removeButton = $("<input type=\"button\" class=\"remove\" value=\"-\" />");
        removeButton.click(function() {
            $(this).parent().remove();
        });
        fieldWrapper.append('<input type="text" name="mytext[]" value="'+$(this).val()+'">');
        fieldWrapper.append(empGoal);
        fieldWrapper.append(removeButton);
        $("#buildyourform").append(fieldWrapper);
    });
   
});

</script>
</head>
<body onload="init()">
  <span style="display: inline-block; vertical-align: top;">
    <div class="handle" style="margin-left: 30px; height: 20px;">Process area</div>
    <div id="myDiagramDiv" style="margin-left: 30px; border: solid 1px #8eb4e3; width:800px; height:500px;
     background-color:#dce6f2;"></div>
  </span>
  <span style="display: inline-block; vertical-align: top;">
    <div class="handle" style="margin-left: 10px; height: 20px;" >Manage process flow</div>
    <div id="myInspectorDiv" class="inspector" style="margin-left: 10px; border: solid 1px #8eb4e3; width:350px; 
         height:150px; background-color:#dce6f2; padding: 20px;"></div> 
    <div><button id="SaveButton" onclick= save() style="margin-left: 180px;">Load</button></div>
  </span>
    <p></p>
    <span style="display: inline-block; vertical-align: top;">
    <div class="handle" style="margin-left: 30px; width:350px; height: 20px;">Process list </div>
    <div id="myDiagramDiv" class="" style="margin-left: 30px; border: solid 1px #8eb4e3; width:330px; 
         height:450px; background-color:#dce6f2; padding: 20px;">
          <?php
            include("connectDB.php");
            $query  = "SELECT *  FROM process_tb ";
            $result = $conn->query($query);
          ?>
          
          <div style="width:300px; height:400px; overflow:auto;">
          <table align="Center">
          <tr>
          <?php
              $i = 1;
              while ($row = $result->fetch_object()) 
              {
                $procID = $row->procID;
                $uph    = $row->uph;
                $numOfhour  = $row->numOfhour;
                $costperProc = $row->costperProc;
                $procname = $row->procname;
                
          ?>
            <?php echo "<td style='width: 30px;'><input type='radio' id= $i  name='proc' 
            value= '$procname' onClick='radiocheck($procID,$uph,$numOfhour,$costperProc);'><td>"; ?>
            <td style="width:300px;"><?php echo $procname;?></td>
          </tr>
          <?php
              $i++;
              }
          ?>
          </table>
          </div>
    </div>
    </span>
    <span style="display: inline-block; vertical-align: top;">
    <div class="handle" style="margin-left: 30px; width:750px; height: 20px;">Property</div>
    <div id="myDiagramDiv" class="" style="margin-left: 30px; border: solid 1px #8eb4e3; width:730px; 
         height:450px; background-color:#dce6f2; padding: 20px;">
         <form action="projDB.php" method="post">
         <table>
         <tr style="height: 30px;">
           <td>Process key</td>
           <td><input type="text" id ="keyProc" name="prockey" readonly style="width: 80px;"></td>
           <td>Process name</td>
           <td><input type="text" readonly id="showProcess" name="procname" style="width: 300px;"></td>
         </tr>
         <tr style="height: 30px;">
           <td>UnitPerHour</td>
           <td><input type="text" id ="showuph" name="uph"></td>
           <td>CostPerUnit</td>
           <td><input type="text" id ="showcostperProc" name="costunit"></td>
         </tr> 
         <tr style="height: 30px;">
           <td>Num of hour</td>
           <td><input type="text" id ="shownumOfhour" name="numhour"></td>
         </tr> 
         </table>
    <br>
    <span style="display: inline-block; vertical-align: top;">
    <div style="width: 250px;">
    <fieldset style="width: 250px;">
    <legend>Select employee:</legend>
    <?php
    include("connectDB.php");
    $query  = "SELECT *  FROM employee_tb ";
    $result = $conn->query($query);
    ?>
    <div style="width:260px; height:250px; overflow:auto;">
    <table align="Center">
    <tr>
    <?php
    $i = 1;
    while ($row = $result->fetch_object()) 
    {
      $fname = $row->fname;
      $lname = $row->lname;
      $fllname = $fname." ".$lname
      ?>
      <?php echo "<td style='width: 50px;'><input type='button' value='$fllname' class='' id='add' 
      style = 'width: 220px; height: 30px;'/> <td>"; ?>
      <!-- <td style="width:100px;"><?php //echo $fname;?></td>
      <td style="width:100px;"><?php //echo $lname;?></td> -->
      </tr>
      <?php
      $i++;
    }
    ?>
    </table>
    </div>
    </fieldset>
    </div>
    </span>
    <span style="display: inline-block; vertical-align: top;">
    <div style="width: 400px; margin-left: 50px;">
    <fieldset id="buildyourform" name="numemp">
    <legend>Employee:</legend>
    <table>
      <th style = width:150px;>name</th>
      <th style = width:150px;>Input</th>
    </table>
    </fieldset>
    </div>
    </span>
    <br>
    <br>
    <center><button type="submit" name="submit"> OK</button></center>
    </form> 
    </div> 
    </span>
    </body>
    </html>
