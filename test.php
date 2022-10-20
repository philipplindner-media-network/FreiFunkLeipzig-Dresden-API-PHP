<?php
$k=$_GET["k"];

if ($k=="") {$k2="0";}else{$k2=$k;}
$stream_opts = [
    "ssl" => [
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ]
];

$apiURL="https://api.freifunk-dresden.de/freifunk-nodes.json";
$json_url=file_get_contents($apiURL,  false, stream_context_create($stream_opts));
$jsonR=json_decode($json_url);

//var_dump($jsonR, true);
$konten_ID      = $jsonR->nodes[$k2]->{"id"};
$knoten_Name    = $jsonR->nodes[$k2]->{"name"};
$knoten_Typ     = $jsonR->nodes[$k2]->{"node_type"};
$knoten_Status  = $jsonR->nodes[$k2]->status->{"online"};
$knoten_Clients = $jsonR->nodes[$k2]->status->{"clients"};
$knoten_Link    = $jsonR->nodes[$k2]->{"href"};
$knoten_lasCon  = $jsonR->nodes[$k2]->status->{"lastcontact"};

if ($knoten_Status=="1")
{
        $knoten_Status_T="<span style='color: #00ff00'>Online</span>";
}
else
{
        $knoten_Status_T="<span style='color: #ff0000'>Offlin</span>";
}

echo "ID: ". $konten_ID ."<br>";
echo "Name: ". $knoten_Name ."<br>";
echo "Type: ". $knoten_Typ ."<br>";
echo "Link: <a href='".$knoten_Link."'>".$knoten_Link."</a><br>";
echo "Status: ".  $knoten_Status_T ."<br>";
echo "Clients: ".  $knoten_Clients ."<br>";
echo "Letzet Kontakt: ".$knoten_lasCon."<br>";
?>
