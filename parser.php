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
        
        
        $sources  = parseSource($tmpArray[2]); //get sources array
        $prevBook = ""; //store the previous book in case
      //a listing has only numbers 
        foreach ($sources as $source) {
            
            $data          = file_get_contents("References/$source.txt"); //get file contents
            $unwantedChars = array(
                ',',
                ':',
                '-'
            ); // create array with unwanted chars
            $tmpSource     = str_replace($unwantedChars, '', $source); // remove them
            
            
            if (!strContains($data, "text") && !strContains($source, "NIV"))  //check if the data has
              //already been called or if the incompatible bible is referenced
            {
                $source = str_replace(" ", "+", $source); //clean up source
                
                
                $my_file = "References/$source.txt"; //get file
                echo "\n<hr />\n"; //formatting
                echo "Sin: ".$tmpArray[1]."\n<br />\n"; //show the sin
                if (is_numeric($tmpSource)) { //check if the tmp source above
                  //is only numbers 
                    $source = $prevBook . " " . $source; //if so the
                  //source needs a book and append prevBook to the front
                }
                $source = trim($source);
                //$source = urlencode($source);
                $source = str_replace(" ", "+", $source);
                
                $handle = fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);
                $source = str_replace("Mt", "Matt", $source);
                $source = str_replace("Mk", "Mrk", $source);
                $source = str_replace("Ro", "Rom", $source);
                $source = str_replace("Pv", "Prov", $source);
                $source = str_replace("Jn", "Jhn", $source);
                $source = str_replace("Ja", "Jas", $source); //replace book bames
                $source = str_replace(":+", ":", $source);
                $source = str_replace("++++", "", $source);
                $source = str_replace("\n", "+", $source);
                $source = str_replace(".", "", $source); //formatting
                if(strContains($source, "Jhn")) //special case for Jhn
                {
                  $sourceSplit = splitString($source, "+"); //split the source
                  $source = $sourceSplit[0]."+".$sourceSplit[1]."+1:".$sourceSplit[2];
                  //keep first two normal and insert the book number before line numbers
                }
                $url = "https://bible-api.com/".$source; //get the final url
                echo $url." | $source"; //show the url and source
                
                $data = file_get_contents($url); //get the data from url
                fwrite($handle, $data); //write the data to the file

                
            }
            
            $sourceArray = splitString($source, "+"); //split up the source
            if (count($sourceArray) == 3) { //if the book has a number before
                $prevBook = $sourceArray[0] . " " . $sourceArray[1]; //the prevbook has a number i.e. 1 Jhn
            } else {
                $prevBook = $sourceArray[0]; //otherwise the book is first part of sourceArray
            }
            
        }
        
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
function printChar($str)
{
    $count = 0;
    while ($count < strlen($str)) {
        echo ord($str[$count]) . " | " . $str[$count] . "\n<br />\n";
        $count++;
    }
}



?>