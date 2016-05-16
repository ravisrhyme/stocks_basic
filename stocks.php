<html>
<head>
    <style type='text/css'>
        form {
            text-align: center;
        }
        table {
         border: 1px solid grey;
         border-collapse: collapse;
         background-color:#F3F3F3;
        }
        th {
            text-align: left;
        }
        td {
            text-align: center;
        }
        .table1 {
            text-align: left;
        }
        .error {
            text-align:center;
        }
        div#dynamic {
        text-align:center;
        }
        .heading{
           text-align:center;
           background-color:#F3F3F3;
           font-size: 25px;
           font-style: italic;
        }
           
    </style>
    <script type='text/javascript'>
        function reset_form(form){
            document.getElementById("dynamic").innerHTML=" "; 
            document.forms["myform"]["company"].value = " ";
        }
        </script>
    </head>
    <body>
        
        <table align=center border=1 cellpadding=10>
            <tr>
            <th class = "heading"> 
                Stock Search
            </th>
                </tr><tr>
            <td>
            <form name="myform" method="get" action=" ">
                Company Name or Symbol: <input type="text" name="company" value="<?php 
                    if (isset($_GET['company'])) {
                        echo $_GET['company'];}
                    else if (isset($_GET['link'])){
                        echo $_GET['link'];
                    }else{
                        echo '';
                    }?>" required ><br>
                <div> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type= "submit" value="search" name="submit">
                <input type= "button" value="clear" name="reset" onclick="reset_form(this.form)">
                </div>
                <div> <br>
                    <a href="http://www.markit.com/product/markit-on-demand">Powered by Markit On Demand</a>
                </div>
            </form>
            </td>
            </tr> 
            
        </table>
        
        <div id="dynamic">    
        <?php
        if(isset($_GET['submit'])){
            //echo " issset() is working";
            $var = trim($_GET["company"]);
            //echo nl2br($var."\n"); 
                $url = "http://dev.markitondemand.com/MODApis/Api/v2/Lookup/xml?input=".$var;
                //echo nl2br($url."\n");
                $xml_info = @file_get_contents($url);
                if (empty($xml_info))
                {
                    die("<br><br><table text-align=center border=1 cellpadding=10 width=60%><tr><td class='error'> No records has been found </td></tr></table>");
                }
                $php_object = simplexml_load_string($xml_info);
                $size = sizeof($php_object->LookupResult);
                
                if ($size == 0)
                {
                    echo "<br><br>";
                    echo "<table align=center border=1 cellpadding=10 width=60%><tr><td class='error'> No records has been found </td></tr></table>";
                    exit;
                }
                echo "<br><br>";
                echo "<table align=center border=1 cellpadding=10><tr>";
                echo "<th class='table1'>Name</th>";
                echo "<th class='table1'>Sybmol</th>";
                echo "<th class='table1'>Exchange</th>";
                echo "<th class='table1'>Details</th></tr>";
            
                $size = sizeof($php_object->LookupResult);
                for ($i = 0; $i < $size; $i++ ){
                    echo "<tr><td class='table1'>";
                echo $php_object->LookupResult[$i]->Name;
                    echo "</td><td class='table1'>";
                echo $php_object->LookupResult[$i]->Symbol;
                    echo "</td><td class='table1'>";
                echo $php_object->LookupResult[$i]->Exchange;
                    echo "</td><td class='table1'>";
                    $symbol = $php_object->LookupResult[$i]->Symbol;
                    //echo $url_detail;
                echo "<a href='stock_final.php?link=$symbol'>More Info</a>";
                echo "</td></tr>";
                //echo nl2br("\n");
            }
            echo "</table>";
        }       
        ?>
            
        <?php
        if(isset($_GET['link'])) {
            $symbol = $_GET['link'];
            $url_detail = "http://dev.markitondemand.com/MODApis/Api/v2/Quote/json?symbol=".$symbol;
            $json_info = @file_get_contents($url_detail);
            $json_content = json_decode($json_info, true);
            if ($json_content["Status"] != "SUCCESS")
            {
                echo "<br><br>";
                echo "<table align=center border=1 cellpadding=10 width=60%><tr><td class='error'> There is no stock information available</td></tr></table>";
                exit;
            }
            echo "<br><br>";
            echo "<table align=center border=1 cellpadding=10 width=60%><tr>";
            echo "<th>Name</th><td>";
            echo $json_content["Name"]."</td></tr>";
            echo "<th>Symbol</th><td>";
            echo $json_content["Symbol"]."</td></tr>";
            echo "<th>Last Price</th><td>";
            echo $json_content["LastPrice"]."</td></tr>";
            echo "<th>Change</th><td>";
            if ($json_content["Change"] < "0" ){
                echo round($json_content["Change"],2)."<img src='http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png' height='15' width='15'></td></tr>";
            }
            else if ($json_content["Change"] > "0") {
                echo round($json_content["Change"],2)."<img src='http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png' height='15' width='15'></td></tr>";
            }
            else {
                echo round($json_content["Change"],2)."</td></tr>";
            }
            echo "<th>Change Percecnt</th><td>";
            if ($json_content["ChangePercent"] < "0" ){
            echo round($json_content["ChangePercent"],2)."%<img src='http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png' height='15' width='15'></td></tr>";
            }
            else if($json_content["ChangePercent"] > "0" ){
            echo round($json_content["ChangePercent"],2)."%<img src='http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png' height='15' width='15'></td></tr>";
            }
            else {
                echo round($json_content["ChangePercent"],2)."%</td></tr>";
            }
            echo "<th>Timestamp</th><td>";
            $time = strtotime($json_content["Timestamp"]);
            echo date("Y-m-d h:i A",$time)." PST</td></tr>";
            echo "<th>MarketCap</th><td>";
            //$marketcap = intval($json_content["MarketCap"]);
            $marketcap_billion = $json_content["MarketCap"]/1000000000;
            if ($marketcap_billion > 0.005){
                echo round($marketcap_billion,2)." B"."</td></tr>";
            }
            else {
             echo round(($marketcap_billion*1000),2)." M"."</td></tr>";   
            }

            echo "<th>Volume</th><td>";
            echo number_format($json_content["Volume"])."</td></tr>";
            echo "<th>Change YTD</th><td>";
            $change_ytd = $json_content["LastPrice"]-$json_content["ChangeYTD"];
            if ($change_ytd < 0){
                echo "(".round($change_ytd,2).")"."<img src='http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png' height='15' width='15'></td></tr>";
            }
            else if ($change_ytd > 0) {
                echo round($change_ytd,2)."<img src='http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png' height='15' width='15'></td></tr>";
            }
            else {
                echo round($change_ytd,2)."</td></tr>";
            }
            echo "<th>Change Percent YTD</th><td>";
            if ($json_content["ChangePercentYTD"] < "0" ){
            echo round($json_content["ChangePercentYTD"],2)."%<img src='http://cs-server.usc.edu:45678/hw/hw6/images/Red_Arrow_Down.png' height='15' width='15'></td></tr>";
            }
            else if ($json_content["ChangePercentYTD"] > "0" ){
                echo round($json_content["ChangePercentYTD"],2)."%<img src='http://cs-server.usc.edu:45678/hw/hw6/images/Green_Arrow_Up.png' height='15' width='15'></td></tr>";
            }
            else {
                echo round($json_content["ChangePercentYTD"],2)."%</td></tr>";
            }
            echo "<th>High</th><td>";
            echo round($json_content["High"],2)."</td></tr>";
            echo "<th>Low</th><td>";
            echo round($json_content["Low"],0)."</td></tr>";
            echo "<th>Open</th><td>";
            echo $json_content["Open"]."</td></tr>";
            echo "</table>";
            
        }
        ?>
     </div>
        
    </body>
</html>