<?php
	use MCServerStatus\MCPing;
	use xPaw\SourceQuery\SourceQuery;
	//include composer autoload
	require_once('../vendor/autoload.php');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<title>Server Status</title>
<link rel="stylesheet" media="all" href="https://www.trance-cat.com/default/style.css" />
<link rel="canonical" href="https://other-game-servers.herokuapp.com/" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://www.trance-cat.com/default/ga.js" async></script>
</head>
<body>
<?php
	//error_reporting(0);
    function ping($host, $port, $timeout=2){
            $fsock = fsockopen($host, $port, $errno, $errstr, $timeout);
            if(!$fsock){
                    return FALSE;
            }else{
                    return TRUE;
            }
    }
	
	function block($des, $host,$port){
		$isup=ping($host, $port);
		echo '<tr>';
		echo '<td data-label="description">'.$des.'</td>';
		echo '<td data-label="status">'.($isup ? '<strong class="green">UP!</strong> ' : '<strong class="red">DOWN!</strong> ').'</td>';
		echo '<td data-label="host"><a href="http://'.$host.'">'.$host.'</a></td>';
		echo '<td data-label="port">'.$port.'</td></tr>';
	}
	
	function mcstatus($host, $port){
		$response=MCPing::check($host);
		
		
		
		$arr = $response->toArray();
		//var_dump($arr);
		$isup=$arr['online'];
		echo '<tr>';
		echo '<td data-label="game">Minecraft'."</td>\n";
		echo '<td data-label="description">'.$arr['motd']."</td>\n";
		echo '<td data-label="status">'.($isup ? '<strong class="green">UP!</strong> ' : '<strong class="red">DOWN!</strong> ')."</td>\n";
		echo '<td data-label="players">'.$arr['players'].'/'.$arr['max_players']."</td>\n";
		echo '<td data-label="host"><a href="http://'.$host.'">'.$host."</a></td>\n";
		echo '<td data-label="port">'.$port."</td>\n";
		echo '<td data-label="player-list">';
		
		if(!is_null($arr['sample_player_list'])){
			foreach($arr['sample_player_list'] as $player){
				echo $player['name']."<br>";
			}
		}
		echo "</td></tr>\n\n";
	}
	
	function sevendaysstatus($host, $port){
		try{
			$SQ_TIMEOUT = 1;
	
			$Query = new SourceQuery();
			$Query->Connect($host, $port, $SQ_TIMEOUT, SourceQuery::SOURCE);
			$sevendaysinfo = $Query->GetInfo();
			//var_dump($sevendaysinfo);
			
			$isup=!empty($sevendaysinfo['HostName']);
			
			//$sevendaysrules=array();
			if($isup==true){
				$sevendaysrules = $Query->GetRules();
				//var_dump($sevendaysrules);
				$time=(float)$sevendaysrules['CurrentServerTime'];
				$day  = (int)(($time/24000)+1);
				$hour = (int)((float)(($time % 24000)/1000));
				$mins = (int)((float)(($time % 1000)*60)/1000);
				if($mins>9){
					echo "<p>Current time in 7D2D</p><p class=\"large-font\">Day ".$day." ".$hour.":".$mins."</p><br>";
				}else{
					echo "<p>Current time in 7D2D</p><p class=\"large-font\">Day ".$day." ".$hour.":0".$mins."</p><br>";
				}
			}
			echo "<tr>\n";
			echo '<td data-label="game">7D2D</td>';
			echo '<td data-label="description">'.$sevendaysinfo['HostName'].' - '.$sevendaysinfo['Map']."</td>\n";
			echo '<td data-label="status">'.($isup ? '<strong class="green">UP!</strong> ' : '<strong class="red">DOWN!</strong> ')."</td>\n";
			echo '<td data-label="players">'.$sevendaysinfo['Players']."/".$sevendaysinfo['MaxPlayers']."</td>\n";
			echo '<td data-label="host"><a href="http://'.$host.'">'.$host.'</a></td>';
			echo '<td data-label="port">'.$port."</td>\n";
			//$players = $Query->GetPlayers();
			echo "<td data-label=\"player-list\">Not available</td></tr>\n\n";
			
			
			
		}catch(Exception $e){
			echo $e->getMessage( );
		}finally{
			$Query->Disconnect( );
		}
	}
	
	?>
<main>
  <article>
    <section class="room width-100 round">
<h1>OTHER at the University of Minnesota - Server Status</h1>
<table>
<thead>
	<tr>
	  <th scope="col">GAME</th>
	  <th scope="col">DESCRIPTION</th>
	  <th scope="col">STATUS</th>
	  <th scope="col">PLAYERS</th>
      <th scope="col">HOST</th>
	  <th scope="col">PORT</th>
	  <th scope="col">PLAYERS</th>
	</tr>
</thead>
<tbody>
<?php sevendaysstatus('128.101.121.222', 26900); ?>
<?php mcstatus('128.101.121.222', 25565); ?>
</tbody>
</table>
</section>
</article>
</main>
</body>
</html>