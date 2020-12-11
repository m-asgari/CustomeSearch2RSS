<?php
use google\CustomSearch\CustomSearch;
require 'CustomSearch.php';
require 'jsonfeed2rss.php';
//Initialize the search class
$cs = new CustomSearch();
$lookingfor ='';
if(!empty($_GET['qtext'])){
    $lookingfor = $_GET['qtext'];
    //echo 'You are looking for : ' . $lookingfor;
} 
else{
    echo "The args was not received";
    return;
};

//Perform a simple search
$json_results = $cs->simpleSearch($lookingfor);
//print_r($json_results);
echo convertrss($json_results)."\n";
//convert_jsonfeed_to_rss($json_results);
//Perform a search with extra parameters
//$response2 = $cs->search('whole Majid', ['excludeTerms'=>'Dr.']);