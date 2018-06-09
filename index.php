<head>
   <link type="text/css" rel="stylesheet" href="http://gigahornet.com/css/bootstrap.min.css">
   <style>
      input{
      width: 60%;
      height: 50px;
      font-size: 40px;
      margin: 3px;
      }
      button{
      width: 60%;
      height: 50px;
      font-size: 40px;
      margin: 3px;
      }
      ::-webkit-input-placeholder {
      text-align: center;
      font-size: 40px;
      }
      :-moz-placeholder { /* Firefox 18- */
      text-align: center;  
      font-size: 40px;
      }
      ::-moz-placeholder {  /* Firefox 19+ */
      text-align: center;  
      font-size: 40px;
      }
      :-ms-input-placeholder {  
      text-align: center; 
      font-size: 40px;
      }
   </style>
   <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
   <script src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>  
</head>


<center>
<h1>
   Sin Search
</h1>
<br />
<form method='post'>
   <input type='text' name='sin' value='ARGUING' placeholder='Sin' /><br />
   <div class='col-md-12' style='border-style: solid; border-width: 1px; border-color: black'>
      <input type='submit' value="Search" name="submit" />
   </div>
</form>
<hr />
<hr />
<?php

$filename    = "sins.html"; //stores the list of sins and sources
$array       = array(); //stores each revalent line from the file
$file        = fopen($filename, "r"); //open file
$x           = fgets($file); //get first line
$write       = false;
$innerCount  = 0;
$sinArray    = array(); //will store all sins
$sourceArray = array(); //will store all sources
  //each element's index correspond to one another
while (!feof($file)) { //go to the end of the file
    
    if (strContains($x, "<tr>")) //this indicates the beginning of an element
    {
        $array[$innerCount] = ""; //give initial value
        $write              = true; //start recording data
    }
    if ($write) {
        $array[$innerCount] = $array[$innerCount] . $x; //put the data into the indicated array
    }
    if (strContains($x, "</tr>")) {
        $write = false; //stop recording data
        $innerCount++; //move onto next element
    } //*/
    $x = fgets($file); // onto next line
    
}
fclose($file);
$outerCount = 0;

foreach ($array as $element) { //go through all elements
    $element = str_replace("</font></td>", "|", $element); //seperates the number and the source 
    $element = str_replace("</font></p></td>", "|", $element); //seperates the sin
    $element = strip_tags($element); //remove all html tags
    
    
    $tmpArray = splitString($element, "|"); //seperate the text
    $number   = str_replace(".", "", $tmpArray[0]); //clean up string
    $number   = trim($number);
    if (is_numeric($number)) 
    {
        $sin = $tmpArray[1]; //stores the sin
        $sin = str_replace("&nbsp;", "", $sin);
        $sin = trim($sin);
        $sin = str_replace("\n", "", $sin);
        $sin = str_replace("   ", "", $sin); //finish cleaning up the sin
    
        $sinArray[$outerCount]    = $sin; //store the sin
        $sourceArray[$outerCount] = $tmpArray[2]; //store the source
        $outerCount++; //onto the next
    }
}
if (isset($_POST["submit"])) { //handle search
    $sin      = $_POST['sin']; //get search sin
    $sin      = strtoupper($sin); //convert to upper
    $positionArray = findElement($sinArray, $sin); //check to see if there is a similar sin
    foreach($positionArray as $position) //go through each potential sin
    {
      $source   = $sourceArray[$position]; //get the source for the sin
      $sources = parseSource($source); //get the sources array 
              echo "Sin: ".$sinArray[$position].""; //show the sin

      foreach ($sources as $source) {
          $file      = "References/$source.txt"; //get source file
          $rawData   = file_get_contents($file); //get json 
          $jsonData  = json_decode($rawData, true); //parse json
          $bibleText = $jsonData["text"]; //access text
        if(!strContains($rawData, "text")) //if there was en error then indicate the file
          //that has an error
        {
          $bibleText = $file;
        }
          echo "<br /><b>$source</b><br />$bibleText<br />";//show the source and the 
        //text

      } //*/
      echo "\n<hr />\n"; //seperate each sin
    }
}

function parseSource($src)
{
    $returnSources = array(); //store the return sources
    $sources       = splitString ($src, ";");
    $count         = 0;
    foreach ($sources as $source) {
        $source                = trim($source); //clean the return source
        $url                   = "$source"; //end of url will be source
        $url                   = str_replace(" ", "+", $url); //make appropriate substitute for url
        $returnSources[$count] = $url; //store in array
        $count++; //onto next element
        
    }
    return $returnSources; //send back the array
}


function strContains($toSearchIn, $toSearchWith)
{
    if (strpos($toSearchIn, $toSearchWith) === false) {
        return false;
    } else {
        return true;
    }
}






function splitString($str, $limit)
{
    $array      = array();
    $count      = 0;
    $innerCount = 0;
    while ($count < strlen($str)) {
        if ($str[$count] == $limit)
        //if current position is the limit
            
        //skip over that character and move to the next element
            
        //in the array
            {
            $count++;
            $innerCount++;
        }
        if ($count >= strlen($str))
        //make sure that the count is not outside of the string
            {
            break; //exit loop
        }
        $array[$innerCount] = $array[$innerCount] . "" . $str[$count]; //add character to the indicated
        //element in the array
        $count++; //onto next character
    }
    return $array;
}
function findElement($arr, $element)
{
    $answerArray = array(); //store responses
    $answerArray[0] = -1; //if there are no returns
    $count = 0; //start at the beginning of the array
    $innerCount = 0; //the position in the answer array
    while ($count < count($arr)) {
        if (strContains($arr[$count], $element)) {
            $answerArray[$innerCount] = $count; //if its a match store 
          //in the answer array
            $innerCount++; //onto next element of the answer array
        }
        $count++; //onto next position in arr
    }
    return $answerArray; //return the potential sin matches
}


?>