<?php
apiURL="http://".$_REQUEST["kid"].".freifunk-dresden.de";
$apiURL2=$apiURL."/sysinfo-json.cgi";
$json_url=file_get_contents($apiURL2);
$jsonR=json_decode($json_url);

//var_dump($jsonR, true);
$FFVesion       =$jsonR->data->firmware->version;
$DestName       =$jsonR->data->firmware->DISTRIB_ID;
$DestVerson     =$jsonR->data->firmware->DISTRIB_RELEASE;
$FFCommunity    =$jsonR->data->common->community;
$FFNode         =$jsonR->data->common->node;
$FFDomain       =$jsonR->data->common->domain;
$FFRouterIP     =$jsonR->data->common->ip;
$FFModell       =$jsonR->data->system->model2;
$FFRuterGPSLat  =$jsonR->data->gps->latitude;
$FFRuterGPSLon  =$jsonR->data->gps->longitude;
$FFRuterName    =$jsonR->data->contact->name;
$FFRuterOrt     =$jsonR->data->contact->location;
$FFRuterEMail   =$jsonR->data->contact->email;
$FFclient2G1m   =$jsonR->data->statistic->client2g->{"1min"};
$FFclient2G1d   =$jsonR->data->statistic->client2g->{"1d"};
$FFclient5G1m   =$jsonR->data->statistic->client5g->{"1min"};
$FFclient5G1d   =$jsonR->data->statistic->client5g->{"1d"};

//Mate
$FFClientAll_1    =$FFclient2G1m+$FFclient5G1m;
$FFClientAll_2    =$FFclient2G1d+$FFclient5G1d;

echo"Freifunk Vesion ".$FFVesion." auf einer ".$DestName." (ver.: ".$DestVerson.")<br>";
echo"auf Ruter: ".$FFModell."<br>";
echo"Community: ".$FFCommunity ."<br>";
echo $FFRuterName." Node: ".$FFNode." <br>";
echo"Ort: ".rawurldecode($FFRuterOrt)." Kontakt: ". rawurldecode($FFRuterEMail)."<br>";
echo"Client:  aktuell:".$FFClientAll_1." Heute: ".$FFClientAll_2."<br>";
echo"Status: ";
if($socket =@ fsockopen($apiURL, 80, $errno, $errstr, 30)) {
        echo "<span style='color: #ff0000'>Offlin</span>";
fclose($socket);
}else{
    echo "<span style='color: #00ff00'>Online</span>";

}
echo"<br>";
echo"<a href='https://karte.freifunk-leipzig.de/grafana/d/KoKOqJc7k/node-public?orgId=1&refresh=30s&from=now-24h&to=now&var-node_id=".$FFNode."'>Statisktik</>
echo"eine Tool von <a href='https://www.philipp-lindner.de'>Philipp Lindner</a> <a href='https://freifunk-dd-le.srv64.de'>FreiFunk-DD-LE.srv64.de</a>";

?>
