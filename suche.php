<?php
$k=$_GET["k"];

if ($k=="") {$k2="5000";}else{$k2=$k;}
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
for ($i=0;$i<=$k2; $i++)
{
        echo "<a href='./test.php?k=". $i ."'>".$i."</a> = Konter ID : ".$jsonR->nodes[$i]->{"name"}."  -  (".$jsonR->nodes[$i]->{"id"}.")<br>";
}
?>
