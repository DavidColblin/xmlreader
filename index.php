<?php
if (!(isset($_POST["title"])))   // if parameter title is not set >> page is not a postback,
{
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<link rel="stylesheet" href="main.css" type="text/css" />

  <title>Christophe & David XML Recipes </title>

  <script language="JavaScript" src="jquery-1.4.2.js" type="text/javascript"></script>
  <script language="JavaScript" type="text/javascript">

  $(function()       // document.ready
  {
    $("#titles").change(function()    //user selects something in list box
    {
      if ($("#titles").val()!="--- Please Select a Recipe ---")
      {
        var data = "title="+$("#titles").val();
        var url="index.php";
        $.post(url,data,function(response)
        {
          // Defining Containers

           var cook =  $(response).find("Recipe").attr("cook") ;
           var contTitle = "" ;
           var contCategory = " ";
           var contItem = "<h4>Ingredients</h4>";
               var contItemName = "";
               var contItemQuantity = "";
           var contStep = "<h4>Steps:</h4>" ;
           var contPreferences ="<h4>Preferences:</h4>";


          //Parsing Values by nodes.
            contTitle += $(response).find("title").text();
            contCategory += $(response).find("category").text();

            $(response).find("item").each(function()
                    {
                    contItemQuantity =     "<li>" + $(this).find("quantity").text();
                    contItemName =      $(this).find("name").text() + "</li>";

                    contItem +=  contItemQuantity + " " + contItemName;
                    });

             $(response).find("step").each(function()
                    {
                    contStep +=     "<li>" + $(this).text() + "</li>";
                    });

             $(response).find("preferences").each(function()
                    {
                    contPreferences +=     "<li>" + $(this).text() + "</li>";
                    });

            //Injecting in html elements
                $("#title").html("<h1>"+contTitle+"</h1>");
                $("#category").html(contCategory + "<i> by Cook "+ cook + "</li>");
                $("#step").html(contStep);
                $("#item").html(contItem);
                $("#preferences").html(contPreferences);


        });

      }
    });
  });
  </script>
</head>

<body>
<h1> <i>Christophe & David XML Recipes Parser. </i></h1>
<div id="main">
      <select id="titles"  size="30%">
      <option>--- Please Select a Recipe ---</option>
      <?php
      $xmlDoc = new DomDocument();         //instantiate
      $xmlDoc->load('recipes.xml') or die("Error: Failed to open list of recipes"); // load dom in file.
      $titles = $xmlDoc->getElementsByTagName('title');

      foreach ($titles as $title)
      {
            echo "<option>",$title->nodeValue,"</option>";  //Add each title to listbox
      }
      ?>
      </select>

      <div id="recipeContents">
          <div id="title"></div>
          <div id="category"></div>
          <div id="item"> </div>
          <div id="step"></div>
          <div id="preferences"></div>
      </div>

</div> <!-- Ends Main DIV-->



</body>
</html>
<?php
}
else    // if postback, title is in payload
{
 $title = $_POST['title'];
 $response="<?xml version='1.0'?>";

 $xmlDocument = new DOMDocument();
 if ($xmlDocument->load('recipes.xml'))
 {
   $xpath = new DOMXPath($xmlDocument);
   $nodeList = $xpath->query("/RECIPES/Recipe[title='$title']", $xmlDocument);
   header('Content-Type: text/xml');
   foreach ($nodeList as $node)
   {
     $response .= $xmlDocument->saveXML($node);
   }
   echo $response;
}



}
?>